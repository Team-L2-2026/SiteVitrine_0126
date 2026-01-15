-- Créez ce fichier : techsolutions_db.sql
-- Exécutez-le dans phpMyAdmin

-- Créer la base si elle n'existe pas
CREATE DATABASE IF NOT EXISTS techsolutions_db;
USE techsolutions_db;

-- Désactiver temporairement les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS = 0;

-- Table administrateurs
DROP TABLE IF EXISTS administrateurs;
CREATE TABLE administrateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'moderateur') DEFAULT 'moderateur',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table clients
DROP TABLE IF EXISTS clients;
CREATE TABLE clients (
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
);

-- Table services
DROP TABLE IF EXISTS services;
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2),
    duree_estimee VARCHAR(50),
    categorie VARCHAR(50),
    icon VARCHAR(50)
);

-- Table demandes (formulaires de contact)
DROP TABLE IF EXISTS demandes;
CREATE TABLE demandes (
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
    notes TEXT,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
);

-- Réactiver les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS = 1;

-- Insérer un administrateur par défaut avec mot de passe MD5
INSERT INTO administrateurs (nom, email, mot_de_passe, role) 
VALUES ('Admin TechSolutions', 'admin@techsolutions.fr', MD5('admin123'), 'admin');

-- Insérer un modérateur de test
INSERT INTO administrateurs (nom, email, mot_de_passe, role) 
VALUES ('Technicien Support', 'support@techsolutions.fr', MD5('support123'), 'moderateur');

-- Insérer des services par défaut
INSERT INTO services (nom, description, prix, duree_estimee, categorie, icon) VALUES
('Infrastructure & Cloud', 'Solutions serveurs et cloud', 199.99, '1-2 semaines', 'infrastructure', 'fa-server'),
('Cybersécurité', 'Protection des données', 299.99, '2-3 semaines', 'securite', 'fa-shield-alt'),
('Support Technique', 'Assistance 24/7', 149.99, 'Immédiat', 'support', 'fa-headset'),
('Sauvegarde & Récupération', 'Solutions de sauvegarde', 179.99, '1 semaine', 'backup', 'fa-database'),
('Réseau & Télécom', 'Conception réseau', 249.99, '2-4 semaines', 'reseau', 'fa-network-wired'),
('Développement Sur Mesure', 'Applications métiers', 399.99, '1-3 mois', 'developpement', 'fa-code');

-- Ajouter quelques clients de test
INSERT INTO clients (nom_entreprise, contact_nom, email, telephone, ville, statut) VALUES
('Entreprise Test 1', 'Jean Dupont', 'jean@entreprise1.fr', '0123456789', 'Paris', 'actif'),
('Entreprise Test 2', 'Marie Martin', 'marie@entreprise2.fr', '0234567891', 'Lyon', 'prospect');

-- Ajouter quelques demandes de test
INSERT INTO demandes (nom, email, telephone, service_id, message, statut) VALUES
('Paul Durand', 'paul@test.fr', '0345678912', 1, 'Bonjour, je souhaite un devis pour une infrastructure cloud.', 'nouvelle'),
('Sophie Bernard', 'sophie@test.fr', '0456789123', 2, 'Besoin d\'un audit de sécurité pour notre réseau.', 'en_cours'),
('Luc Petit', 'luc@test.fr', '0567891234', 3, 'Problème avec notre serveur, besoin d\'assistance urgente.', 'traitee');

-- Vérification finale
SELECT '=== ADMINISTRATEURS ===' as '';
SELECT id, nom, email, role, DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation FROM administrateurs;

SELECT '=== SERVICES ===' as '';
SELECT id, nom, prix, categorie FROM services ORDER BY id;

SELECT '=== CLIENTS ===' as '';
SELECT id, nom_entreprise, contact_nom, email, statut FROM clients;

SELECT '=== DEMANDES ===' as '';
SELECT d.id, d.nom, d.email, s.nom as service, d.statut, DATE_FORMAT(d.date_demande, '%d/%m/%Y %H:%i') as date_demande
FROM demandes d
LEFT JOIN services s ON d.service_id = s.id;

SELECT '=== RÉSUMÉ ===' as '';
SELECT 
    (SELECT COUNT(*) FROM administrateurs) as total_admins,
    (SELECT COUNT(*) FROM services) as total_services,
    (SELECT COUNT(*) FROM clients) as total_clients,
    (SELECT COUNT(*) FROM demandes) as total_demandes;