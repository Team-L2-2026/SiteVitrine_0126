<?php
// ===========================
// CONFIGURATION & CONNEXION BD
// ===========================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session AU DÉBUT
session_start();

// Paramètres XAMPP
$host = 'localhost';
$dbname = 'techsolutions_db';
$username = 'root';
$password = '';

// Fonction pour créer la BD et les tables
function initDatabase($host, $username, $password, $dbname) {
    try {
        // Connexion sans sélectionner de base
        $pdo_temp = new PDO("mysql:host=$host", $username, $password);
        $pdo_temp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Créer la base si elle n'existe pas
        $pdo_temp->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        $pdo_temp->exec("USE `$dbname`");
        
        // Table utilisateurs (conservée mais pas affichée)
        $pdo_temp->exec("
            CREATE TABLE IF NOT EXISTS utilisateurs (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nom VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                mot_de_passe VARCHAR(255) NOT NULL,
                role ENUM('admin', 'technicien') DEFAULT 'technicien',
                date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Table clients
        $pdo_temp->exec("
            CREATE TABLE IF NOT EXISTS clients (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nom_entreprise VARCHAR(150) NOT NULL,
                contact_nom VARCHAR(100) NOT NULL,
                contact_prenom VARCHAR(100),
                email VARCHAR(100) NOT NULL,
                telephone VARCHAR(20),
                adresse TEXT,
                ville VARCHAR(100),
                code_postal VARCHAR(10),
                date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                statut ENUM('actif', 'inactif', 'prospect') DEFAULT 'prospect'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Table services
        $pdo_temp->exec("
            CREATE TABLE IF NOT EXISTS services (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nom VARCHAR(100) NOT NULL,
                description TEXT,
                prix DECIMAL(10,2),
                duree_estimee VARCHAR(50),
                categorie VARCHAR(50),
                icon VARCHAR(50)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Table demandes
        $pdo_temp->exec("
            CREATE TABLE IF NOT EXISTS demandes (
                id INT PRIMARY KEY AUTO_INCREMENT,
                client_id INT NULL,
                nom VARCHAR(100) NOT NULL,
                entreprise VARCHAR(150),
                email VARCHAR(100) NOT NULL,
                telephone VARCHAR(20) NOT NULL,
                service_id INT,
                message TEXT NOT NULL,
                date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                statut ENUM('nouvelle', 'en_cours', 'traitee', 'annulee') DEFAULT 'nouvelle',
                notes TEXT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        return $pdo_temp;
        
    } catch(PDOException $e) {
        die("Erreur d'initialisation : " . $e->getMessage());
    }
}

// Fonction pour peupler la base avec des données initiales
function populateDatabase($pdo) {
    try {
        // Vérifier si admin existe (créé silencieusement, pas affiché)
        $check = $pdo->query("SELECT COUNT(*) as count FROM utilisateurs WHERE email = 'admin@techsolutions.fr'")->fetch();
        if ($check['count'] == 0) {
            $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)")
                 ->execute(['Administrateur', 'admin@techsolutions.fr', $passwordHash, 'admin']);
        }
        
        // Vérifier si services existent
        $checkServices = $pdo->query("SELECT COUNT(*) as count FROM services")->fetch();
        if ($checkServices['count'] == 0) {
            $services = [
                ['Infrastructure & Cloud', 'Solutions serveurs et cloud', 199.99, '1-2 semaines', 'infrastructure', 'fa-server'],
                ['Cybersécurité', 'Protection des données', 299.99, '2-3 semaines', 'securite', 'fa-shield-alt'],
                ['Support Technique', 'Assistance 24/7', 149.99, 'Immédiat', 'support', 'fa-headset'],
                ['Sauvegarde & Récupération', 'Solutions de sauvegarde', 179.99, '1 semaine', 'backup', 'fa-database'],
                ['Réseau & Télécom', 'Conception réseau', 249.99, '2-4 semaines', 'reseau', 'fa-network-wired'],
                ['Développement Sur Mesure', 'Applications métiers', 399.99, '1-3 mois', 'developpement', 'fa-code']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO services (nom, description, prix, duree_estimee, categorie, icon) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($services as $service) {
                $stmt->execute($service);
            }
        }
        
    } catch(Exception $e) {
        // Ignorer les erreurs de peuplement
    }
}

// Connexion principale
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Peupler avec des données
    populateDatabase($pdo);
    
} catch(PDOException $e) {
    // Si la base n'existe pas, la créer
    if ($e->getCode() == '1049') { // Unknown database
        $pdo = initDatabase($host, $username, $password, $dbname);
        populateDatabase($pdo);
    } else {
        die("Erreur de connexion : " . $e->getMessage());
    }
}

// ===========================
// TRAITEMENT DU FORMULAIRE
// ===========================
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
            // Vérifier si client existe
            $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
            $stmt->execute([$data['email']]);
            $client = $stmt->fetch();
            
            $client_id = null;
            if (!$client) {
                // Nouveau client avec adresse et ville
                $stmt = $pdo->prepare("
                    INSERT INTO clients (nom_entreprise, contact_nom, email, telephone, adresse, ville, code_postal, statut) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'prospect')
                ");
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
            
            // Insérer la demande
            $stmt = $pdo->prepare("
                INSERT INTO demandes (client_id, nom, entreprise, email, telephone, service_id, message) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $success = $stmt->execute([
                $client_id,
                $data['nom'],
                $data['entreprise'],
                $data['email'],
                $data['telephone'],
                $data['service_id'],
                $data['message']
            ]);
            
            if ($success) {
                $message = "✅ Votre demande a été enregistrée avec succès ! Nous vous contacterons rapidement.";
                $messageType = 'success';
                $_POST = []; // Réinitialiser
            } else {
                $message = "❌ Une erreur est survenue lors de l'enregistrement.";
                $messageType = 'error';
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

// Récupérer les services
try {
    $services = $pdo->query("SELECT * FROM services ORDER BY id")->fetchAll();
} catch(Exception $e) {
    $services = [];
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
        
        /* Hero */
        .hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white; padding: 160px 0 100px; text-align: center;
        }
        .hero h1 { font-size: 3rem; margin-bottom: 20px; }
        .hero p { font-size: 1.2rem; margin-bottom: 30px; opacity: 0.9; }
        
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
            .hero h1 { font-size: 2.2rem; }
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

    <!-- Hero Section -->
    <section id="accueil" class="hero">
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
                <div style="text-align: center; padding: 40px; background: #f1f5f9; border-radius: 10px;">
                    <p>Chargement des services...</p>
                    <p><small>La base de données est en cours d'initialisation</small></p>
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
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nom">Nom complet *</label>
                            <input type="text" id="nom" name="nom" class="form-control" 
                                   value="<?= $_POST['nom'] ?? '' ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="entreprise">Entreprise</label>
                            <input type="text" id="entreprise" name="entreprise" class="form-control"
                                   value="<?= $_POST['entreprise'] ?? '' ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-control"
                                       value="<?= $_POST['email'] ?? '' ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="telephone">Téléphone *</label>
                                <input type="tel" id="telephone" name="telephone" class="form-control"
                                       value="<?= $_POST['telephone'] ?? '' ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" class="form-control"
                                   value="<?= $_POST['adresse'] ?? '' ?>" placeholder="Rue, numéro">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="ville">Ville</label>
                                <input type="text" id="ville" name="ville" class="form-control"
                                       value="<?= $_POST['ville'] ?? '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="code_postal">Code postal</label>
                                <input type="text" id="code_postal" name="code_postal" class="form-control"
                                       value="<?= $_POST['code_postal'] ?? '' ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="service_id">Service intéressé *</label>
                            <select id="service_id" name="service_id" class="form-control" required>
                                <option value="">Sélectionnez un service</option>
                                <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id'] ?>"
                                    <?= (($_POST['service_id'] ?? '') == $service['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['nom']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Description de vos besoins *</label>
                            <textarea id="message" name="message" class="form-control" rows="5" required><?= $_POST['message'] ?? '' ?></textarea>
                        </div>
                        
                        <button type="submit" name="submit_form" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Envoyer la demande
                        </button>
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