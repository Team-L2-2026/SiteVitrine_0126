-- Créez ce fichier : bd_techsolutions.sql
-- Exécutez-le dans phpMyAdmin

CREATE DATABASE IF NOT EXISTS techsolutions_db;
USE techsolutions_db;

-- Table utilisateurs (pour connexion admin)
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'technicien') DEFAULT 'technicien',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table clients
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
CREATE TABLE demandes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NULL, -- Peut être null si pas encore client
    nom VARCHAR(100) NOT NULL,
    entreprise VARCHAR(150),
    email VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    service_id INT,
    message TEXT NOT NULL,
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('nouvelle', 'en_cours', 'traitee', 'annulee') DEFAULT 'nouvelle',
    notes TEXT,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- Table interventions (si besoin plus tard)
CREATE TABLE interventions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    demande_id INT,
    technicien_id INT,
    date_intervention DATE,
    heure_debut TIME,
    heure_fin TIME,
    description TEXT,
    statut ENUM('planifiee', 'en_cours', 'terminee', 'annulee') DEFAULT 'planifiee',
    FOREIGN KEY (demande_id) REFERENCES demandes(id),
    FOREIGN KEY (technicien_id) REFERENCES utilisateurs(id)
);