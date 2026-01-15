<?php
// api_dashboard.php - API COMPLÈTE POUR DASHBOARD AVEC GESTION ADMINS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

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
    
    // Récupérer la méthode HTTP
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Récupérer l'admin connecté (simplifié)
    $currentAdminId = null;
    function getCurrentAdminId($pdo) {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        
        if (!empty($token) && strpos($token, 'Bearer ') === 0) {
            $token = str_replace('Bearer ', '', $token);
            // Décoder le token simple
            $decoded = base64_decode($token);
            if ($decoded) {
                $parts = explode(':', $decoded);
                if (count($parts) >= 1) {
                    $email = $parts[0];
                    $stmt = $pdo->prepare("SELECT id FROM administrateurs WHERE email = ?");
                    $stmt->execute([$email]);
                    $admin = $stmt->fetch();
                    return $admin ? $admin['id'] : null;
                }
            }
        }
        return null;
    }
    
    $currentAdminId = getCurrentAdminId($pdo);
    
    // ========== GET: Récupérer les données ==========
    if ($method === 'GET') {
        
        // GET /api_dashboard.php?section=utilisateurs
        if (isset($_GET['section'])) {
            $section = $_GET['section'];
            
            if ($section === 'utilisateurs') {
                $stmt = $pdo->query("SELECT id, nom, email, role, date_creation FROM administrateurs ORDER BY date_creation DESC");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $data]);
                
            } elseif ($section === 'demandes') {
                $stmt = $pdo->query("
                    SELECT d.*, s.nom as service_nom, c.nom_entreprise 
                    FROM demandes d 
                    LEFT JOIN services s ON d.service_id = s.id 
                    LEFT JOIN clients c ON d.client_id = c.id 
                    ORDER BY d.date_demande DESC
                ");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $data]);
                
            } elseif ($section === 'services') {
                $stmt = $pdo->query("SELECT * FROM services ORDER BY id");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $data]);
                
            } elseif ($section === 'clients') {
                $stmt = $pdo->query("SELECT * FROM clients ORDER BY date_inscription DESC");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $data]);
                
            } elseif ($section === 'statistiques') {
                // Nombre total de demandes
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM demandes");
                $total = $stmt->fetch()['total'];
                
                // Demandes par statut
                $stmt = $pdo->query("SELECT statut, COUNT(*) as count FROM demandes GROUP BY statut");
                $parStatut = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Demandes récentes (7 derniers jours)
                $stmt = $pdo->query("SELECT COUNT(*) as recent FROM demandes WHERE date_demande >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                $recent = $stmt->fetch()['recent'];
                
                // Services les plus demandés
                $stmt = $pdo->query("
                    SELECT s.nom, COUNT(d.id) as count 
                    FROM demandes d 
                    JOIN services s ON d.service_id = s.id 
                    GROUP BY s.id 
                    ORDER BY count DESC 
                    LIMIT 5
                ");
                $topServices = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Clients par statut
                $stmt = $pdo->query("SELECT statut, COUNT(*) as count FROM clients GROUP BY statut");
                $clientsStatut = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'total_demandes' => $total,
                        'demandes_recentes' => $recent,
                        'demandes_par_statut' => $parStatut,
                        'services_populaires' => $topServices,
                        'clients_par_statut' => $clientsStatut,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ]);
            }
        }
        
        // GET /api_dashboard.php?search=keyword
        elseif (isset($_GET['search'])) {
            $search = '%' . $_GET['search'] . '%';
            $stmt = $pdo->prepare("
                SELECT d.*, s.nom as service_nom 
                FROM demandes d 
                LEFT JOIN services s ON d.service_id = s.id 
                WHERE d.nom LIKE ? OR d.email LIKE ? OR d.message LIKE ? 
                ORDER BY d.date_demande DESC
            ");
            $stmt->execute([$search, $search, $search]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
        }
        
        // GET /api_dashboard.php (retourne toutes les données)
        else {
            // Récupérer les compteurs
            $counts = [];
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM administrateurs");
            $counts['utilisateurs'] = $stmt->fetch()['count'];
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM demandes");
            $counts['demandes'] = $stmt->fetch()['count'];
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM services");
            $counts['services'] = $stmt->fetch()['count'];
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM clients");
            $counts['clients'] = $stmt->fetch()['count'];
            
            // Demandes non traitées
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM demandes WHERE statut = 'nouvelle'");
            $counts['alertes'] = $stmt->fetch()['count'];
            
            echo json_encode(['success' => true, 'counts' => $counts]);
        }
    }
    
    // ========== POST: Créer/modifier des données ==========
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action'])) {
            $action = $data['action'];
            
            if ($action === 'login') {
                // Login admin
                if (empty($data['email']) || empty($data['password'])) {
                    echo json_encode(['success' => false, 'error' => 'Email et mot de passe requis']);
                    exit;
                }
                
                $stmt = $pdo->prepare("SELECT id, nom, email FROM administrateurs WHERE email = ? AND mot_de_passe = MD5(?)");
                $stmt->execute([$data['email'], $data['password']]);
                $admin = $stmt->fetch();
                
                if ($admin) {
                    // Générer un token (simplifié)
                    $token = base64_encode($admin['email'] . ':' . time());
                    
                    echo json_encode([
                        'success' => true,
                        'token' => $token,
                        'admin' => [
                            'id' => $admin['id'],
                            'nom' => $admin['nom'],
                            'email' => $admin['email']
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Identifiants incorrects']);
                }
                
            } elseif ($action === 'update_demande') {
                // Mettre à jour une demande
                if (empty($data['id']) || empty($data['statut'])) {
                    echo json_encode(['success' => false, 'error' => 'ID et statut requis']);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE demandes SET statut = ?, notes = ? WHERE id = ?");
                $stmt->execute([
                    $data['statut'],
                    $data['notes'] ?? '',
                    $data['id']
                ]);
                
                echo json_encode(['success' => true, 'message' => 'Demande mise à jour']);
                
            } elseif ($action === 'update_service') {
                // Mettre à jour un service
                if (empty($data['id']) || empty($data['nom']) || empty($data['prix'])) {
                    echo json_encode(['success' => false, 'error' => 'Champs requis manquants']);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE services SET nom = ?, description = ?, prix = ?, duree_estimee = ?, categorie = ?, icon = ? WHERE id = ?");
                $stmt->execute([
                    $data['nom'],
                    $data['description'] ?? '',
                    $data['prix'],
                    $data['duree_estimee'] ?? '',
                    $data['categorie'] ?? '',
                    $data['icon'] ?? 'fa-cog',
                    $data['id']
                ]);
                
                echo json_encode(['success' => true, 'message' => 'Service mis à jour']);
                
            } elseif ($action === 'add_service') {
                // Ajouter un service
                if (empty($data['nom']) || empty($data['prix'])) {
                    echo json_encode(['success' => false, 'error' => 'Nom et prix requis']);
                    exit;
                }
                
                $stmt = $pdo->prepare("INSERT INTO services (nom, description, prix, duree_estimee, categorie, icon) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['nom'],
                    $data['description'] ?? '',
                    $data['prix'],
                    $data['duree_estimee'] ?? '',
                    $data['categorie'] ?? '',
                    $data['icon'] ?? 'fa-cog'
                ]);
                
                echo json_encode(['success' => true, 'message' => 'Service ajouté', 'id' => $pdo->lastInsertId()]);
                
            } elseif ($action === 'add_admin') {
                // Ajouter un administrateur
                if (empty($data['nom']) || empty($data['email']) || empty($data['password'])) {
                    echo json_encode(['success' => false, 'error' => 'Tous les champs sont requis']);
                    exit;
                }
                
                if (strlen($data['password']) < 6) {
                    echo json_encode(['success' => false, 'error' => 'Le mot de passe doit avoir au moins 6 caractères']);
                    exit;
                }
                
                // Vérifier si l'email existe déjà
                $stmt = $pdo->prepare("SELECT id FROM administrateurs WHERE email = ?");
                $stmt->execute([$data['email']]);
                if ($stmt->fetch()) {
                    echo json_encode(['success' => false, 'error' => 'Cet email est déjà utilisé']);
                    exit;
                }
                
                // Hasher le mot de passe
                $passwordHash = md5($data['password']);
                
                $stmt = $pdo->prepare("INSERT INTO administrateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $data['nom'],
                    $data['email'],
                    $passwordHash,
                    $data['role'] ?? 'moderateur'
                ]);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Administrateur ajouté avec succès',
                    'id' => $pdo->lastInsertId()
                ]);
                
            } elseif ($action === 'update_admin') {
                // Mettre à jour un administrateur
                if (empty($data['id']) || empty($data['nom']) || empty($data['email'])) {
                    echo json_encode(['success' => false, 'error' => 'ID, nom et email requis']);
                    exit;
                }
                
                // Vérifier si l'email existe déjà (pour un autre admin)
                $stmt = $pdo->prepare("SELECT id FROM administrateurs WHERE email = ? AND id != ?");
                $stmt->execute([$data['email'], $data['id']]);
                if ($stmt->fetch()) {
                    echo json_encode(['success' => false, 'error' => 'Cet email est déjà utilisé']);
                    exit;
                }
                
                // Si mot de passe fourni, le mettre à jour
                if (!empty($data['password'])) {
                    if (strlen($data['password']) < 6) {
                        echo json_encode(['success' => false, 'error' => 'Le mot de passe doit avoir au moins 6 caractères']);
                        exit;
                    }
                    $passwordHash = md5($data['password']);
                    $stmt = $pdo->prepare("UPDATE administrateurs SET nom = ?, email = ?, mot_de_passe = ?, role = ? WHERE id = ?");
                    $stmt->execute([
                        $data['nom'],
                        $data['email'],
                        $passwordHash,
                        $data['role'] ?? 'moderateur',
                        $data['id']
                    ]);
                } else {
                    $stmt = $pdo->prepare("UPDATE administrateurs SET nom = ?, email = ?, role = ? WHERE id = ?");
                    $stmt->execute([
                        $data['nom'],
                        $data['email'],
                        $data['role'] ?? 'moderateur',
                        $data['id']
                    ]);
                }
                
                echo json_encode(['success' => true, 'message' => 'Administrateur mis à jour']);
            }
        }
    }
    
    // ========== DELETE: Supprimer des données ==========
    elseif ($method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $input);
        $id = $input['id'] ?? $_GET['id'] ?? null;
        
        if (isset($_GET['type'])) {
            $type = $_GET['type'];
            
            if ($type === 'demande' && $id) {
                $stmt = $pdo->prepare("DELETE FROM demandes WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true, 'message' => 'Demande supprimée']);
                
            } elseif ($type === 'service' && $id) {
                // Vérifier si le service est utilisé
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM demandes WHERE service_id = ?");
                $stmt->execute([$id]);
                $used = $stmt->fetch()['count'];
                
                if ($used > 0) {
                    echo json_encode(['success' => false, 'error' => 'Ce service est utilisé dans des demandes']);
                } else {
                    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
                    $stmt->execute([$id]);
                    echo json_encode(['success' => true, 'message' => 'Service supprimé']);
                }
                
            } elseif ($type === 'admin' && $id) {
                // Vérifier s'il reste au moins un admin
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM administrateurs");
                $count = $stmt->fetch()['count'];
                
                if ($count <= 1) {
                    echo json_encode(['success' => false, 'error' => 'Impossible de supprimer le dernier administrateur']);
                    exit;
                }
                
                // Empêcher l'admin de se supprimer lui-même
                if ($id == $currentAdminId) {
                    echo json_encode(['success' => false, 'error' => 'Vous ne pouvez pas supprimer votre propre compte']);
                    exit;
                }
                
                $stmt = $pdo->prepare("DELETE FROM administrateurs WHERE id = ?");
                $stmt->execute([$id]);
                
                echo json_encode(['success' => true, 'message' => 'Administrateur supprimé']);
            }
        }
    }
    
    else {
        echo json_encode(['success' => false, 'error' => 'Méthode non supportée']);
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>