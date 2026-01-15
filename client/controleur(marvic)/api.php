<?php
// td/api.php - API SIMPLIFIÉE POUR FORMULAIRE
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Connexion BD
$host = 'localhost';
$dbname = 'techsolutions_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ========== POST: Créer une demande ==========
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validation
        if (empty($data['nom']) || empty($data['email']) || empty($data['telephone']) || empty($data['message']) || empty($data['service_id'])) {
            echo json_encode(['success' => false, 'error' => 'Tous les champs obligatoires sont requis']);
            exit;
        }
        
        // 1. Vérifier/Créer client
        $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
        $stmt->execute([$data['email']]);
        $client = $stmt->fetch();
        
        if (!$client) {
            $stmt = $pdo->prepare("INSERT INTO clients (nom_entreprise, contact_nom, email, telephone, adresse, ville, code_postal, statut) VALUES (?, ?, ?, ?, ?, ?, ?, 'prospect')");
            $stmt->execute([
                $data['entreprise'] ?? 'Particulier',
                $data['nom'],
                $data['email'],
                $data['telephone'],
                $data['adresse'] ?? '',
                $data['ville'] ?? '',
                $data['code_postal'] ?? ''
            ]);
            $client_id = $pdo->lastInsertId();
        } else {
            $client_id = $client['id'];
        }
        
        // 2. Créer la demande
        $stmt = $pdo->prepare("INSERT INTO demandes (client_id, nom, entreprise, email, telephone, service_id, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $client_id,
            $data['nom'],
            $data['entreprise'] ?? '',
            $data['email'],
            $data['telephone'],
            $data['service_id'],
            $data['message']
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Demande enregistrée avec succès ! Nous vous contacterons sous 24h.',
            'id' => $pdo->lastInsertId()
        ]);
    }
    
    // ========== DELETE: Supprimer une demande ==========
    elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        
        $stmt = $pdo->prepare("DELETE FROM demandes WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => "Demande #$id supprimée avec succès"
        ]);
    }
    
    // ========== GET: Lister les demandes ==========
    elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET)) {
        $stmt = $pdo->query("SELECT d.*, s.nom as service_nom FROM demandes d LEFT JOIN services s ON d.service_id = s.id ORDER BY d.date_demande DESC");
        $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($demandes);
    }
    
    // ========== GET: Lister les services ==========
    elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['services'])) {
        $stmt = $pdo->query("SELECT * FROM services ORDER BY id");
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($services);
    }
    
    else {
        echo json_encode(['error' => 'Action non reconnue']);
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>