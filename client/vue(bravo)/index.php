<?php
// ===========================
// PAGE PRINCIPALE - UTILISE L'API
// ===========================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// URL de l'API - MODIFIEZ CECI SELON VOTRE CONFIGURATION
// Pour XAMPP/WAMP, essayez ces options :
// $api_url = 'http://localhost/td/api.php';
// $api_url = 'http://127.0.0.1/techsolutions/td/api.php';
// $api_url = './api.php'; // Si dans le même dossier

$api_url = 'http://localhost/td/api.php';

// Débogage
echo "<!-- API URL: $api_url -->\n";

// Récupérer les services via l'API
function fetchServices($api_url) {
    echo "<!-- Début fetchServices -->\n";
    
    // Option 1: Utiliser file_get_contents (plus simple)
    try {
        $url = $api_url . '?services=1';
        echo "<!-- Tentative URL: $url -->\n";
        
        // Désactiver SSL verification pour localhost
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === FALSE) {
            echo "<!-- Erreur file_get_contents -->\n";
            $error = error_get_last();
            echo "<!-- Error: " . ($error['message'] ?? 'Inconnu') . " -->\n";
            
            // Option 2: Essayer avec cURL
            return fetchServicesCurl($api_url);
        }
        
        echo "<!-- Réponse API reçue -->\n";
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "<!-- Erreur JSON: " . json_last_error_msg() . " -->\n";
            echo "<!-- Réponse brute: " . htmlspecialchars(substr($response, 0, 200)) . " -->\n";
            return [];
        }
        
        echo "<!-- Services trouvés: " . count($data) . " -->\n";
        return is_array($data) ? $data : [];
        
    } catch (Exception $e) {
        echo "<!-- Exception: " . $e->getMessage() . " -->\n";
        return [];
    }
}

// Fonction alternative avec cURL
function fetchServicesCurl($api_url) {
    echo "<!-- Tentative avec cURL -->\n";
    
    if (!function_exists('curl_init')) {
        echo "<!-- cURL non disponible -->\n";
        return [];
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . '?services=1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    echo "<!-- cURL HTTP Code: $httpCode -->\n";
    if ($error) {
        echo "<!-- cURL Error: $error -->\n";
    }
    
    curl_close($ch);
    
    if ($response && $httpCode == 200) {
        $data = json_decode($response, true);
        return is_array($data) ? $data : [];
    }
    
    return [];
}

// OPTION 3: Récupérer directement depuis la base de données (sans API)
function fetchServicesDirect() {
    echo "<!-- Tentative connexion directe BD -->\n";
    
    $host = 'localhost';
    $dbname = 'techsolutions_db';
    $username = 'root';
    $password = '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SELECT * FROM services ORDER BY id");
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<!-- Services BD trouvés: " . count($services) . " -->\n";
        return $services;
        
    } catch(PDOException $e) {
        echo "<!-- Erreur BD: " . $e->getMessage() . " -->\n";
        return [];
    }
}

// Essayer d'abord avec l'API, puis directement depuis la BD
$services = fetchServices($api_url);

// Si pas de services via API, essayer directement
if (empty($services)) {
    echo "<!-- Aucun service via API, tentative directe BD -->\n";
    $services = fetchServicesDirect();
}

// Si toujours pas de services, créer des données par défaut
if (empty($services)) {
    echo "<!-- Création de services par défaut -->\n";
    $services = [
        [
            'id' => 1,
            'nom' => 'Infrastructure & Cloud',
            'description' => 'Solutions serveurs et cloud',
            'prix' => 199.99,
            'duree_estimee' => '1-2 semaines',
            'categorie' => 'infrastructure',
            'icon' => 'fa-server'
        ],
        [
            'id' => 2,
            'nom' => 'Cybersécurité',
            'description' => 'Protection des données',
            'prix' => 299.99,
            'duree_estimee' => '2-3 semaines',
            'categorie' => 'securite',
            'icon' => 'fa-shield-alt'
        ],
        [
            'id' => 3,
            'nom' => 'Support Technique',
            'description' => 'Assistance 24/7',
            'prix' => 149.99,
            'duree_estimee' => 'Immédiat',
            'categorie' => 'support',
            'icon' => 'fa-headset'
        ]
    ];
}

// Traitement du formulaire
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_form'])) {
    try {
        $data = [
            'nom' => htmlspecialchars(trim($_POST['nom'] ?? '')),
            'entreprise' => htmlspecialchars(trim($_POST['entreprise'] ?? '')),
            'email' => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'telephone' => htmlspecialchars(trim($_POST['telephone'] ?? '')),
            'adresse' => htmlspecialchars(trim($_POST['adresse'] ?? '')),
            'ville' => htmlspecialchars(trim($_POST['ville'] ?? '')),
            'code_postal' => htmlspecialchars(trim($_POST['code_postal'] ?? '')),
            'service_id' => (int)($_POST['service_id'] ?? 0),
            'message' => htmlspecialchars(trim($_POST['message'] ?? ''))
        ];
        
        // Validation
        $errors = [];
        if (empty($data['nom'])) $errors[] = "Le nom est requis";
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
        if (empty($data['telephone'])) $errors[] = "Le téléphone est requis";
        if (empty($data['message'])) $errors[] = "Le message est requis";
        if ($data['service_id'] == 0) $errors[] = "Veuillez sélectionner un service";
        
        if (empty($errors)) {
            // Option 1: Envoyer à l'API
            $apiSuccess = false;
            
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode == 200) {
                    $result = json_decode($response, true);
                    if (isset($result['success']) && $result['success']) {
                        $message = "✅ " . $result['message'];
                        $messageType = 'success';
                        $_POST = [];
                        $apiSuccess = true;
                    }
                }
            } catch (Exception $e) {
                // Continuer avec l'option 2
            }
            
            // Option 2: Sauvegarder directement en BD si l'API échoue
            if (!$apiSuccess) {
                try {
                    $host = 'localhost';
                    $dbname = 'techsolutions_db';
                    $username = 'root';
                    $password = '';
                    
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Vérifier/Créer client
                    $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
                    $stmt->execute([$data['email']]);
                    $client = $stmt->fetch();
                    
                    if (!$client) {
                        $stmt = $pdo->prepare("INSERT INTO clients (nom_entreprise, contact_nom, email, telephone, adresse, ville, code_postal, statut) VALUES (?, ?, ?, ?, ?, ?, ?, 'prospect')");
                        $stmt->execute([
                            $data['entreprise'] ?: 'Particulier',
                            $data['nom'],
                            $data['email'],
                            $data['telephone'],
                            $data['adresse'],
                            $data['ville'],
                            $data['code_postal']
                        ]);
                        $client_id = $pdo->lastInsertId();
                    } else {
                        $client_id = $client['id'];
                    }
                    
                    // Créer la demande
                    $stmt = $pdo->prepare("INSERT INTO demandes (client_id, nom, entreprise, email, telephone, service_id, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $client_id,
                        $data['nom'],
                        $data['entreprise'],
                        $data['email'],
                        $data['telephone'],
                        $data['service_id'],
                        $data['message']
                    ]);
                    
                    $message = "✅ Votre demande a été enregistrée avec succès ! Nous vous contacterons sous 24h.";
                    $messageType = 'success';
                    $_POST = [];
                    
                } catch (Exception $e) {
                    $message = "❌ Erreur lors de l'enregistrement: " . $e->getMessage();
                    $messageType = 'error';
                }
            }
        } else {
            $message = "❌ " . implode('<br>', $errors);
            $messageType = 'error';
        }
        
    } catch(Exception $e) {
        $message = "❌ Erreur technique : " . $e->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechSolutions Pro - Services Informatiques</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ... (TOUT LE CSS RESTE IDENTIQUE À LA VERSION PRÉCÉDENTE) ... */
        /* COLLEZ ICI TOUT LE CSS DE LA VERSION AVEC CARROUSEL */
        
        :root {
            --primary: #2563eb; --secondary: #1e40af; --accent: #3b82f6;
            --dark: #1e293b; --light: #f8fafc; --gray: #64748b;
            --success: #10b981; --danger: #ef4444; --warning: #f59e0b;
            --whatsapp: #25D366; --facebook: #1877F2; --twitter: #1DA1F2;
            --transition: all 0.3s ease;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: var(--light); color: var(--dark); line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        section { padding: 80px 0; }
        
        /* Header */
        header {
            background: rgba(255, 255, 255, 0.95);
            position: fixed; width: 100%; top: 0; z-index: 1000;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
        .logo { font-size: 28px; font-weight: 800; color: var(--primary); }
        .logo i { color: var(--accent); margin-right: 10px; }
        .nav-links { display: flex; gap: 30px; }
        .nav-links a { text-decoration: none; color: var(--dark); font-weight: 600; }
        .nav-links a:hover { color: var(--primary); }
        .cta-button {
            background: var(--primary); color: white; padding: 10px 25px;
            border-radius: 50px; text-decoration: none; font-weight: 700;
            transition: var(--transition);
        }
        .cta-button:hover { background: var(--secondary); transform: translateY(-2px); }
        
        /* Hero avec Carrousel */
        .hero {
            position: relative;
            height: 100vh;
            min-height: 700px;
            padding: 160px 0 100px;
            overflow: hidden;
            color: white;
            display: flex;
            align-items: center;
            text-align: center;
        }
        
        .carousel-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .carousel-slide.active {
            opacity: 1;
        }
        
        /* Images de fond du carrousel */
        .slide-1 {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                              url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
        }
        
        .slide-2 {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                              url('https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
        }
        
        .slide-3 {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                              url('https://images.unsplash.com/photo-1535223289827-42f1e9919769?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
        }
        
        .slide-4 {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                              url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
        }
        
        .slide-5 {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                              url('https://images.unsplash.com/photo-1518709268805-4e9042af2176?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .hero h1 { 
            font-size: 3.5rem; 
            margin-bottom: 20px; 
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            animation: fadeInUp 1s ease-out;
        }
        
        .hero p { 
            font-size: 1.3rem; 
            margin-bottom: 30px; 
            opacity: 0.9;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        
        .hero .cta-button {
            animation: fadeInUp 1s ease-out 0.6s both;
            background: var(--primary);
            border: 2px solid white;
            font-size: 1.1rem;
            padding: 15px 35px;
        }
        
        .hero .cta-button:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
        }
        
        /* Contrôles du carrousel */
        .carousel-controls {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            display: flex;
            gap: 15px;
        }
        
        .carousel-indicators {
            display: flex;
            gap: 10px;
        }
        
        .carousel-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .carousel-indicator.active {
            background: white;
            transform: scale(1.3);
        }
        
        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 3;
        }
        
        .carousel-nav button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }
        
        .carousel-nav button:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.1);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Services */
        .services-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px; margin-top: 50px;
        }
        .service-card {
            background: white; border-radius: 15px; padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: var(--transition);
        }
        .service-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .service-icon {
            width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px; font-size: 28px; color: white;
        }
        .service-card h3 { font-size: 1.5rem; margin-bottom: 15px; color: var(--dark); }
        .service-card p { color: var(--gray); margin-bottom: 15px; }
        .price { color: var(--primary); font-weight: 700; font-size: 1.2rem; margin: 10px 0; }
        
        /* Formulaire */
        .contact-form {
            background: white; padding: 40px; border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-control {
            width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0;
            border-radius: 10px; font-size: 16px; transition: var(--transition);
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .submit-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white; border: none; padding: 15px; border-radius: 10px;
            font-size: 18px; font-weight: 700; cursor: pointer; width: 100%;
            transition: var(--transition);
        }
        .submit-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3); }
        
        /* Messages */
        .alert {
            padding: 15px 20px; border-radius: 10px; margin-bottom: 30px;
            text-align: center;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 2px solid #ef4444; }
        
        /* Footer */
        footer {
            background: var(--dark); color: white; padding: 60px 0 30px;
            margin-top: 50px;
        }
        
        /* Réseaux sociaux */
        .social-buttons {
            display: flex; gap: 15px; margin-top: 30px;
        }
        .social-btn {
            width: 50px; height: 50px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; text-decoration: none; font-size: 20px;
            transition: var(--transition);
        }
        .social-btn:hover { transform: translateY(-5px); }
        .whatsapp { background: var(--whatsapp); }
        .facebook { background: var(--facebook); }
        .twitter { background: var(--twitter); }
        .linkedin { background: #0077B5; }
        .instagram { background: linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D); }
        
        /* Contact info */
        .contact-info-item {
            display: flex; align-items: flex-start; gap: 15px;
            margin-bottom: 20px;
        }
        .contact-info-item i {
            width: 40px; height: 40px; background: var(--primary);
            color: white; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .hero p { font-size: 1.1rem; }
            .carousel-nav { display: none; }
            .nav-links { display: none; }
            .contact-form { padding: 20px; }
            section { padding: 60px 0; }
            .form-row { grid-template-columns: 1fr; }
        }
        
        /* Bouton WhatsApp flottant */
        .whatsapp-float {
            position: fixed; bottom: 30px; right: 30px;
            width: 60px; height: 60px; background: var(--whatsapp);
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; color: white; font-size: 28px;
            text-decoration: none; box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
            z-index: 1000; transition: var(--transition);
        }
        .whatsapp-float:hover { transform: scale(1.1); }
        
        /* Chargement */
        .loading {
            text-align: center; padding: 40px; background: #f1f5f9; 
            border-radius: 10px; color: var(--gray);
        }
        
        /* Debug info */
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Bouton WhatsApp flottant -->
    <a href="https://wa.me/33123456789" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Header -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <i class="fas fa-laptop-code"></i>
                    TechSolutions Pro
                </div>
                <div class="nav-links">
                    <a href="#accueil">Accueil</a>
                    <a href="#services">Services</a>
                    <a href="#contact">Contact</a>
                    <a href="#formulaire" class="cta-button">Demander un devis</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section avec Carrousel -->
    <section id="accueil" class="hero">
        <!-- Carrousel d'images -->
        <div class="carousel-container">
            <div class="carousel-slide slide-1 active"></div>
            <div class="carousel-slide slide-2"></div>
            <div class="carousel-slide slide-3"></div>
            <div class="carousel-slide slide-4"></div>
            <div class="carousel-slide slide-5"></div>
        </div>
        
        <!-- Navigation du carrousel -->
        <div class="carousel-nav">
            <button class="prev-btn"><i class="fas fa-chevron-left"></i></button>
            <button class="next-btn"><i class="fas fa-chevron-right"></i></button>
        </div>
        
        <!-- Indicateurs du carrousel -->
        <div class="carousel-controls">
            <div class="carousel-indicators">
                <div class="carousel-indicator active" data-slide="0"></div>
                <div class="carousel-indicator" data-slide="1"></div>
                <div class="carousel-indicator" data-slide="2"></div>
                <div class="carousel-indicator" data-slide="3"></div>
                <div class="carousel-indicator" data-slide="4"></div>
            </div>
        </div>
        
        <!-- Contenu Hero -->
        <div class="container">
            <div class="hero-content">
                <h1>Solutions Informatiques Professionnelles</h1>
                <p>Nous transformons votre infrastructure IT avec des solutions sur mesure, une maintenance proactive et un support 24/7.</p>
                <a href="#formulaire" class="cta-button">Demander un devis gratuit</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 50px; color: var(--dark);">
                Nos Services Expert
            </h2>
            
            <?php if (empty($services)): ?>
                <div class="loading">
                    <p><i class="fas fa-spinner fa-spin"></i> Chargement des services...</p>
                    <p><small>Si les services n'apparaissent pas, vérifiez que la base de données est installée</small></p>
                    
                    <!-- Informations de débogage -->
                    <div class="debug-info">
                        <strong>Problèmes possibles :</strong><br>
                        1. Vérifiez que la base de données 'techsolutions_db' existe<br>
                        2. Vérifiez que la table 'services' existe<br>
                        3. Vérifiez les identifiants MySQL (root/sans mot de passe par défaut)<br>
                        4. Testez l'API directement : <a href="<?= $api_url ?>?services=1" target="_blank"><?= $api_url ?>?services=1</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas <?= htmlspecialchars($service['icon']) ?>"></i>
                        </div>
                        <h3><?= htmlspecialchars($service['nom']) ?></h3>
                        <p><?= htmlspecialchars($service['description']) ?></p>
                        <div class="price">À partir de <?= number_format($service['prix'], 2, ',', ' ') ?> €</div>
                        <small style="color: var(--gray);">Durée : <?= htmlspecialchars($service['duree_estimee']) ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Informations de débogage (caché par défaut) -->
                <div class="debug-info" style="display: none; margin-top: 30px;">
                    <strong>Informations techniques :</strong><br>
                    Nombre de services chargés : <?= count($services) ?><br>
                    Source : <?= isset($services[0]['id']) ? 'Base de données' : 'Données par défaut' ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 50px; color: var(--dark);">
                Contactez-nous
            </h2>
            
            <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= $message ?>
            </div>
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px;">
                <!-- Formulaire avec adresse et ville -->
                <div id="formulaire" class="contact-form">
                    <h3 style="margin-bottom: 30px; color: var(--dark);">Demande de devis gratuit</h3>
                    <form method="POST" action="" id="contactForm">
                        <div class="form-group">
                            <label for="nom">Nom complet *</label>
                            <input type="text" id="nom" name="nom" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="entreprise">Entreprise</label>
                            <input type="text" id="entreprise" name="entreprise" class="form-control"
                                   value="<?= htmlspecialchars($_POST['entreprise'] ?? '') ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-control"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="telephone">Téléphone *</label>
                                <input type="tel" id="telephone" name="telephone" class="form-control"
                                       value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" class="form-control"
                                   value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>" placeholder="Rue, numéro">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="ville">Ville</label>
                                <input type="text" id="ville" name="ville" class="form-control"
                                       value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="code_postal">Code postal</label>
                                <input type="text" id="code_postal" name="code_postal" class="form-control"
                                       value="<?= htmlspecialchars($_POST['code_postal'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="service_id">Service intéressé *</label>
                            <select id="service_id" name="service_id" class="form-control" required>
                                <option value="">Sélectionnez un service</option>
                                <?php if (!empty($services)): ?>
                                    <?php foreach ($services as $service): ?>
                                    <option value="<?= $service['id'] ?>"
                                        <?= (($_POST['service_id'] ?? '') == $service['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($service['nom']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Description de vos besoins *</label>
                            <textarea id="message" name="message" class="form-control" rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" name="submit_form" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Envoyer la demande
                        </button>
                        
                        <!-- Note sur la méthode d'envoi -->
                        <p style="margin-top: 15px; font-size: 0.9em; color: var(--gray); text-align: center;">
                            <i class="fas fa-info-circle"></i> Les données sont sauvegardées dans votre base de données locale.
                        </p>
                    </form>
                </div>
                
                <!-- Informations et réseaux sociaux -->
                <div>
                    <h3 style="margin-bottom: 20px; color: var(--dark);">Nos coordonnées</h3>
                    
                    <div class="contact-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Adresse</strong>
                            <p>123 Avenue de la Technologie<br>75000 Paris, France</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Téléphone</strong>
                            <p>01 23 45 67 89<br>Urgences : 06 12 34 56 78</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong>
                            <p>contact@techsolutions-pro.fr<br>support@techsolutions-pro.fr</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Horaires d'ouverture</strong>
                            <p>Lundi - Vendredi : 8h00 - 19h00<br>Samedi : 9h00 - 13h00<br>Urgences 24h/24 - 7j/7</p>
                        </div>
                    </div>
                    
                    <h3 style="margin-top: 40px; margin-bottom: 20px; color: var(--dark);">
                        Suivez-nous sur les réseaux
                    </h3>
                    
                    <div class="social-buttons">
                        <a href="https://facebook.com" class="social-btn facebook" target="_blank" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com" class="social-btn twitter" target="_blank" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://linkedin.com" class="social-btn linkedin" target="_blank" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://instagram.com" class="social-btn instagram" target="_blank" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/33123456789" class="social-btn whatsapp" target="_blank" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                    
                    <div style="margin-top: 40px; padding: 25px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 15px;">
                        <h4 style="margin-bottom: 15px; color: var(--primary);">
                            <i class="fas fa-headset"></i> Support rapide
                        </h4>
                        <p style="margin-bottom: 10px;">
                            <i class="fas fa-check-circle" style="color: var(--success);"></i> 
                            Réponse sous 24h maximum
                        </p>
                        <p style="margin-bottom: 10px;">
                            <i class="fas fa-check-circle" style="color: var(--success);"></i> 
                            Devis gratuit sans engagement
                        </p>
                        <p>
                            <i class="fas fa-check-circle" style="color: var(--success);"></i> 
                            Intervention sous 48h pour les urgences
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px;">
                <div>
                    <div class="logo" style="color: white; margin-bottom: 20px; font-size: 24px;">
                        <i class="fas fa-laptop-code"></i>
                        TechSolutions Pro
                    </div>
                    <p style="opacity: 0.9; margin-bottom: 20px;">
                        Votre partenaire informatique de confiance pour la transformation digitale de votre entreprise.
                    </p>
                    
                    <div class="social-buttons" style="margin-top: 20px;">
                        <a href="https://facebook.com" class="social-btn facebook" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com" class="social-btn twitter" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://linkedin.com" class="social-btn linkedin" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 style="margin-bottom: 15px; color: white;">Services</h4>
                    <p><a href="#services" style="color: rgba(255,255,255,0.8); text-decoration: none; display: block; margin-bottom: 8px;">
                        <i class="fas fa-server fa-xs"></i> Infrastructure & Cloud
                    </a></p>
                    <p><a href="#services" style="color: rgba(255,255,255,0.8); text-decoration: none; display: block; margin-bottom: 8px;">
                        <i class="fas fa-shield-alt fa-xs"></i> Cybersécurité
                    </a></p>
                    <p><a href="#services" style="color: rgba(255,255,255,0.8); text-decoration: none; display: block; margin-bottom: 8px;">
                        <i class="fas fa-headset fa-xs"></i> Support Technique
                    </a></p>
                    <p><a href="#services" style="color: rgba(255,255,255,0.8); text-decoration: none; display: block; margin-bottom: 8px;">
                        <i class="fas fa-code fa-xs"></i> Développement
                    </a></p>
                </div>
                
                <div>
                    <h4 style="margin-bottom: 15px; color: white;">Contact rapide</h4>
                    <p style="margin-bottom: 8px;">
                        <i class="fas fa-phone fa-xs" style="color: rgba(255,255,255,0.8);"></i> 
                        <a href="tel:0123456789" style="color: rgba(255,255,255,0.8); text-decoration: none;">
                            01 23 45 67 89
                        </a>
                    </p>
                    <p style="margin-bottom: 8px;">
                        <i class="fas fa-envelope fa-xs" style="color: rgba(255,255,255,0.8);"></i> 
                        <a href="mailto:contact@techsolutions-pro.fr" style="color: rgba(255,255,255,0.8); text-decoration: none;">
                            contact@techsolutions-pro.fr
                        </a>
                    </p>
                    <p style="margin-bottom: 8px;">
                        <i class="fab fa-whatsapp fa-xs" style="color: rgba(255,255,255,0.8);"></i> 
                        <a href="https://wa.me/33123456789" style="color: rgba(255,255,255,0.8); text-decoration: none;">
                            WhatsApp : 06 12 34 56 78
                        </a>
                    </p>
                </div>
            </div>
            
            <div style="text-align: center; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.1);">
                <p>&copy; 2024 TechSolutions Pro. Tous droits réservés.</p>
                <p style="font-size: 0.9em; color: rgba(255,255,255,0.7); margin-top: 10px;">
                    <i class="fas fa-map-marker-alt"></i> 123 Avenue de la Technologie, 75000 Paris | 
                    <i class="fas fa-clock"></i> <?= date('d/m/Y H:i') ?>
                </p>
            </div>
        </div>
    </footer>

    <script>
        // ====================================
        // CARROUSEL AUTOMATIQUE ET MANUEL
        // ====================================
        
        class Carousel {
            constructor() {
                this.slides = document.querySelectorAll('.carousel-slide');
                this.indicators = document.querySelectorAll('.carousel-indicator');
                this.prevBtn = document.querySelector('.prev-btn');
                this.nextBtn = document.querySelector('.next-btn');
                this.currentIndex = 0;
                this.totalSlides = this.slides.length;
                this.interval = null;
                this.slideDuration = 5000; // 5 secondes entre chaque slide
                
                this.init();
            }
            
            init() {
                // Initialisation
                this.showSlide(this.currentIndex);
                this.startAutoSlide();
                
                // Événements pour les boutons de navigation
                if (this.prevBtn) {
                    this.prevBtn.addEventListener('click', () => this.prevSlide());
                }
                
                if (this.nextBtn) {
                    this.nextBtn.addEventListener('click', () => this.nextSlide());
                }
                
                // Événements pour les indicateurs
                this.indicators.forEach((indicator, index) => {
                    indicator.addEventListener('click', () => this.goToSlide(index));
                });
                
                // Pause au survol
                const carousel = document.querySelector('.carousel-container');
                if (carousel) {
                    carousel.addEventListener('mouseenter', () => this.stopAutoSlide());
                    carousel.addEventListener('mouseleave', () => this.startAutoSlide());
                }
            }
            
            showSlide(index) {
                // Masquer toutes les slides
                this.slides.forEach(slide => slide.classList.remove('active'));
                this.indicators.forEach(indicator => indicator.classList.remove('active'));
                
                // Afficher la slide active
                this.slides[index].classList.add('active');
                this.indicators[index].classList.add('active');
                this.currentIndex = index;
            }
            
            nextSlide() {
                const nextIndex = (this.currentIndex + 1) % this.totalSlides;
                this.showSlide(nextIndex);
                this.restartAutoSlide();
            }
            
            prevSlide() {
                const prevIndex = (this.currentIndex - 1 + this.totalSlides) % this.totalSlides;
                this.showSlide(prevIndex);
                this.restartAutoSlide();
            }
            
            goToSlide(index) {
                this.showSlide(index);
                this.restartAutoSlide();
            }
            
            startAutoSlide() {
                this.stopAutoSlide(); // Arrêter d'abord s'il y a déjà un intervalle
                this.interval = setInterval(() => this.nextSlide(), this.slideDuration);
            }
            
            stopAutoSlide() {
                if (this.interval) {
                    clearInterval(this.interval);
                    this.interval = null;
                }
            }
            
            restartAutoSlide() {
                this.stopAutoSlide();
                this.startAutoSlide();
            }
        }
        
        // Initialiser le carrousel quand la page est chargée
        document.addEventListener('DOMContentLoaded', () => {
            new Carousel();
        });
        
        // ====================================
        // AUTRES FONCTIONNALITÉS
        // ====================================
        
        // Animation du header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.98)';
                header.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
            }
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Validation du formulaire
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Réinitialiser les styles d'erreur
                document.querySelectorAll('.form-control').forEach(input => {
                    input.style.borderColor = '#e2e8f0';
                });
                
                // Validation des champs obligatoires
                const requiredFields = ['nom', 'email', 'telephone', 'message', 'service_id'];
                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field && (field.value.trim() === '' || field.value === '')) {
                        field.style.borderColor = '#ef4444';
                        isValid = false;
                    }
                });
                
                // Validation email
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email && !emailRegex.test(email.value)) {
                    email.style.borderColor = '#ef4444';
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Veuillez remplir correctement tous les champs obligatoires (*).');
                }
            });
        }
        
        // Animation pour les cartes de service
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Animation pour les boutons sociaux
        document.querySelectorAll('.social-btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.1)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>
</html>