<?php
session_start();

// Si pas de session, vérifier le localStorage via JavaScript
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard TechSolutions Pro</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-7QPST09RCL"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-7QPST09RCL');
    </script>
    
    <style>
        :root {
            --blue: #2563eb;
            --indigo: #4f46e5;
            --purple: #7c3aed;
            --pink: #db2777;
            --red: #dc2626;
            --orange: #ea580c;
            --yellow: #d97706;
            --green: #059669;
            --teal: #0d9488;
            --cyan: #0891b2;
            --gray: #6b7280;
            --light: #f8fafc;
            --dark: #1e293b;
            --bg: #f1f5f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
            line-height: 1.6;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, var(--indigo) 0%, var(--blue) 100%);
            color: white;
            padding: 25px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: relative;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo i {
            font-size: 32px;
            color: white;
        }

        .logo h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .welcome {
            font-size: 16px;
            opacity: 0.9;
        }

        #userName {
            font-weight: 600;
            color: white;
        }

        /* Boutons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: white;
            color: var(--blue);
        }

        .btn-primary:hover {
            background: var(--light);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--red);
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 14px;
        }

        /* Dashboard Grid */
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            padding: 35px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: white;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .card-count {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0;
        }

        .card-subtitle {
            font-size: 14px;
            color: var(--gray);
        }

        /* Couleurs des cartes */
        .card-users .card-icon { background: linear-gradient(135deg, var(--blue), var(--indigo)); }
        .card-demands .card-icon { background: linear-gradient(135deg, var(--green), var(--teal)); }
        .card-services .card-icon { background: linear-gradient(135deg, var(--orange), var(--yellow)); }
        .card-clients .card-icon { background: linear-gradient(135deg, var(--purple), var(--pink)); }
        .card-alerts .card-icon { background: linear-gradient(135deg, var(--red), #ef4444); }
        .card-stats .card-icon { background: linear-gradient(135deg, var(--cyan), #06b6d4); }

        /* Sections */
        .section {
            display: none;
            background: white;
            margin: 0 35px 35px;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f5f9;
        }

        .section-header h2 {
            color: var(--dark);
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #f8fafc;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8fafc;
        }

        /* Badges */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-secondary { background: #e5e7eb; color: #374151; }

        /* Search bar */
        .search-container {
            margin-bottom: 20px;
        }

        .search-input {
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            width: 250px;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Stat cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: var(--blue);
            margin: 10px 0;
        }

        .stat-label {
            color: var(--gray);
            font-size: 14px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: var(--gray);
        }

        .loading i {
            font-size: 24px;
            margin-bottom: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                padding: 20px;
                gap: 15px;
            }

            .section {
                margin: 0 20px 20px;
                padding: 20px;
            }

            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .user-info {
                flex-direction: column;
                gap: 10px;
            }

            table {
                display: block;
                overflow-x: auto;
            }
            
            .section-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-lock"></i> Connexion Admin</h2>
                <button class="close-modal" onclick="closeModal('loginModal')">&times;</button>
            </div>
            <form id="loginForm">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" class="form-control" required placeholder="admin@techsolutions.fr">
                </div>
                <div class="form-group">
                    <label for="loginPassword">Mot de passe</label>
                    <input type="password" id="loginPassword" class="form-control" required placeholder="Votre mot de passe">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
                <p style="text-align: center; margin-top: 15px; color: var(--gray); font-size: 14px;">
                    Identifiants par défaut :<br>
                    admin@techsolutions.fr / admin123
                </p>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="editModalTitle">Modifier</h2>
                <button class="close-modal" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form id="editForm">
                <div id="editFormContent">
                    <!-- Le contenu du formulaire sera généré dynamiquement -->
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Header -->
    <header>

    <!-- Ajoutez après la ligne du bouton déconnexion -->
<a href="https://analytics.google.com/analytics/web/" target="_blank" class="btn" 
   style="background: linear-gradient(135deg, #4285F4, #34A853); color: white;">
    <i class="fas fa-chart-bar"></i> Analytics
</a>
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-laptop-code"></i>
                <h1>TechSolutions Pro - Dashboard</h1>
            </div>
            <div class="user-info">
                <div class="welcome">
                    Bienvenue, <span id="userName">Administrateur</span>
                </div>
                <button id="logoutBtn" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </div>
        </div>
    </header>

    <!-- Dashboard Cards -->
    <div class="dashboard">
        <div class="card card-users" onclick="loadSection('utilisateurs')">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>Administrateurs</h3>
            <div class="card-count" id="countUsers">0</div>
            <div class="card-subtitle">Utilisateurs système</div>
        </div>

        <div class="card card-demands" onclick="loadSection('demandes')">
            <div class="card-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h3>Demandes</h3>
            <div class="card-count" id="countDemands">0</div>
            <div class="card-subtitle">Nouvelles demandes</div>
        </div>

        <div class="card card-services" onclick="loadSection('services')">
            <div class="card-icon">
                <i class="fas fa-cogs"></i>
            </div>
            <h3>Services</h3>
            <div class="card-count" id="countServices">0</div>
            <div class="card-subtitle">Services disponibles</div>
        </div>

        <div class="card card-clients" onclick="loadSection('clients')">
            <div class="card-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3>Clients</h3>
            <div class="card-count" id="countClients">0</div>
            <div class="card-subtitle">Clients enregistrés</div>
        </div>

        <div class="card card-alerts" onclick="loadSection('alertes')">
            <div class="card-icon">
                <i class="fas fa-bell"></i>
            </div>
            <h3>Alertes</h3>
            <div class="card-count" id="countAlerts">0</div>
            <div class="card-subtitle">Demandes non traitées</div>
        </div>

        <div class="card card-stats" onclick="loadSection('statistiques')">
            <div class="card-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3>Statistiques</h3>
            <div class="card-count" id="countStats">-</div>
            <div class="card-subtitle">Analyses complètes</div>
        </div>
    </div>

    <!-- Sections -->
    <div id="section-utilisateurs" class="section">
        <div class="section-header">
            <h2><i class="fas fa-users"></i> Administrateurs</h2>
            <div class="section-actions">
                <button class="btn btn-primary" onclick="openAddAdminModal()">
                    <i class="fas fa-plus"></i> Ajouter un admin
                </button>
                <input type="text" class="search-input" placeholder="Rechercher..." onkeyup="searchUsers(this.value)">
            </div>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <!-- Les données seront chargées ici -->
                </tbody>
            </table>
        </div>
    </div>

    <div id="section-demandes" class="section">
        <div class="section-header">
            <h2><i class="fas fa-file-alt"></i> Demandes des clients</h2>
            <div class="section-actions">
                <input type="text" class="search-input" placeholder="Rechercher une demande..." onkeyup="searchDemandes(this.value)">
            </div>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="demandesTableBody">
                    <!-- Les données seront chargées ici -->
                </tbody>
            </table>
        </div>
    </div>

    <div id="section-services" class="section">
        <div class="section-header">
            <h2><i class="fas fa-cogs"></i> Services</h2>
            <div class="section-actions">
                <button class="btn btn-primary" onclick="openAddServiceModal()">
                    <i class="fas fa-plus"></i> Ajouter un service
                </button>
            </div>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Prix (€)</th>
                        <th>Durée</th>
                        <th>Catégorie</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="servicesTableBody">
                    <!-- Les données seront chargées ici -->
                </tbody>
            </table>
        </div>
    </div>

    <div id="section-clients" class="section">
        <div class="section-header">
            <h2><i class="fas fa-user-tie"></i> Clients</h2>
            <div class="section-actions">
                <input type="text" class="search-input" placeholder="Rechercher un client..." onkeyup="searchClients(this.value)">
            </div>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Entreprise</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Ville</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="clientsTableBody">
                    <!-- Les données seront chargées ici -->
                </tbody>
            </table>
        </div>
    </div>

    <div id="section-alertes" class="section">
        <div class="section-header">
            <h2><i class="fas fa-bell"></i> Alertes</h2>
        </div>
        <div id="alertesContent">
            <!-- Les alertes seront chargées ici -->
        </div>
    </div>

    <div id="section-statistiques" class="section">
        <div class="section-header">
            <h2><i class="fas fa-chart-line"></i> Statistiques</h2>
            <div class="section-actions">
                <button class="btn btn-primary" onclick="refreshStats()">
                    <i class="fas fa-sync-alt"></i> Actualiser
                </button>
            </div>
        </div>
        <div id="statsContent">
            <!-- Les statistiques seront chargées ici -->
        </div>
    </div>

    <script>
        // Configuration
        const API_URL = 'http://localhost/td/api_dashboard.php';
        let currentToken = null;
        let currentAdmin = null;

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier si déjà connecté
            const savedToken = localStorage.getItem('adminToken');
            const savedAdmin = localStorage.getItem('adminData');
            
            if (savedToken && savedAdmin) {
                try {
                    currentToken = savedToken;
                    currentAdmin = JSON.parse(savedAdmin);
                    document.getElementById('userName').textContent = currentAdmin.nom;
                    loadDashboardData();
                } catch (e) {
                    showLoginModal();
                }
            } else {
                showLoginModal();
            }
            
            // Écouteurs d'événements
            document.getElementById('logoutBtn').addEventListener('click', logout);
            document.getElementById('loginForm').addEventListener('submit', handleLogin);
            document.getElementById('editForm').addEventListener('submit', handleEditForm);
        });

        // ========== FONCTIONS D'AUTHENTIFICATION ==========
        
        function showLoginModal() {
            document.getElementById('loginModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        async function handleLogin(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'login',
                        email: email,
                        password: password
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    currentToken = result.token;
                    currentAdmin = result.admin;
                    
                    // Sauvegarder dans localStorage
                    localStorage.setItem('adminToken', currentToken);
                    localStorage.setItem('adminData', JSON.stringify(currentAdmin));
                    
                    // Mettre à jour l'interface
                    document.getElementById('userName').textContent = currentAdmin.nom;
                    closeModal('loginModal');
                    loadDashboardData();
                    
                    // Google Analytics
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'admin_login', { admin_email: currentAdmin.email });
                    }
                } else {
                    alert('Erreur: ' + (result.error || 'Identifiants incorrects'));
                }
            } catch (error) {
                console.error('Erreur de connexion:', error);
                alert('Erreur de connexion au serveur');
            }
        }

        function logout() {
            if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
                localStorage.removeItem('adminToken');
                localStorage.removeItem('adminData');
                currentToken = null;
                currentAdmin = null;
                
                // Google Analytics
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'admin_logout');
                }
                
                showLoginModal();
                hideAllSections();
            }
        }

        // ========== FONCTIONS DU DASHBOARD ==========
        
        async function loadDashboardData() {
            try {
                const response = await fetch(API_URL, {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Mettre à jour les compteurs
                    if (result.counts) {
                        document.getElementById('countUsers').textContent = result.counts.utilisateurs || 0;
                        document.getElementById('countDemands').textContent = result.counts.demandes || 0;
                        document.getElementById('countServices').textContent = result.counts.services || 0;
                        document.getElementById('countClients').textContent = result.counts.clients || 0;
                        document.getElementById('countAlerts').textContent = result.counts.alertes || 0;
                    }
                }
            } catch (error) {
                console.error('Erreur chargement dashboard:', error);
            }
        }

        function hideAllSections() {
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
        }

        // ========== CHARGEMENT DES SECTIONS ==========
        
        async function loadSection(section) {
            hideAllSections();
            
            // Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'open_section', { section: section });
            }
            
            const sectionElement = document.getElementById('section-' + section);
            sectionElement.classList.add('active');
            
            // Charger les données selon la section
            switch(section) {
                case 'utilisateurs':
                    await loadUtilisateurs();
                    break;
                case 'demandes':
                    await loadDemandes();
                    break;
                case 'services':
                    await loadServices();
                    break;
                case 'clients':
                    await loadClients();
                    break;
                case 'alertes':
                    await loadAlertes();
                    break;
                case 'statistiques':
                    await loadStatistiques();
                    break;
            }
        }

        async function loadUtilisateurs() {
            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = '<tr><td colspan="6" class="loading"><i class="fas fa-spinner fa-spin"></i> Chargement...</td></tr>';
            
            try {
                const response = await fetch(API_URL + '?section=utilisateurs', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    let html = '';
                    result.data.forEach(user => {
                        const isCurrentUser = currentAdmin && user.id == currentAdmin.id;
                        html += `
                            <tr>
                                <td>${user.id}</td>
                                <td>${escapeHtml(user.nom)} ${isCurrentUser ? '<span class="badge badge-info">(Vous)</span>' : ''}</td>
                                <td>${escapeHtml(user.email)}</td>
                                <td><span class="badge ${user.role === 'admin' ? 'badge-danger' : 'badge-secondary'}">${user.role}</span></td>
                                <td>${formatDate(user.date_creation)}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="editAdmin(${user.id})" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    ${!isCurrentUser ? `
                                        <button class="btn btn-danger btn-sm" onclick="deleteAdmin(${user.id})" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    ` : ''}
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="6">Aucun utilisateur trouvé</td></tr>';
                }
            } catch (error) {
                console.error('Erreur:', error);
                tbody.innerHTML = '<tr><td colspan="6">Erreur de chargement</td></tr>';
            }
        }

        async function loadDemandes() {
            const tbody = document.getElementById('demandesTableBody');
            tbody.innerHTML = '<tr><td colspan="8" class="loading"><i class="fas fa-spinner fa-spin"></i> Chargement...</td></tr>';
            
            try {
                const response = await fetch(API_URL + '?section=demandes', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    let html = '';
                    result.data.forEach(demande => {
                        const statutClass = getStatutClass(demande.statut);
                        const statutText = getStatutText(demande.statut);
                        
                        html += `
                            <tr>
                                <td>${demande.id}</td>
                                <td>${escapeHtml(demande.nom)}</td>
                                <td>${escapeHtml(demande.service_nom || 'N/A')}</td>
                                <td>${escapeHtml(demande.email)}</td>
                                <td>${escapeHtml(demande.telephone)}</td>
                                <td>${formatDate(demande.date_demande)}</td>
                                <td><span class="badge ${statutClass}">${statutText}</span></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="editDemande(${demande.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteDemande(${demande.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="8">Aucune demande trouvée</td></tr>';
                }
            } catch (error) {
                console.error('Erreur:', error);
                tbody.innerHTML = '<tr><td colspan="8">Erreur de chargement</td></tr>';
            }
        }

        async function loadServices() {
            const tbody = document.getElementById('servicesTableBody');
            tbody.innerHTML = '<tr><td colspan="7" class="loading"><i class="fas fa-spinner fa-spin"></i> Chargement...</td></tr>';
            
            try {
                const response = await fetch(API_URL + '?section=services', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    let html = '';
                    result.data.forEach(service => {
                        html += `
                            <tr>
                                <td>${service.id}</td>
                                <td><strong>${escapeHtml(service.nom)}</strong></td>
                                <td>${escapeHtml(service.description || '')}</td>
                                <td>${parseFloat(service.prix).toFixed(2)} €</td>
                                <td>${escapeHtml(service.duree_estimee || '')}</td>
                                <td><span class="badge badge-info">${escapeHtml(service.categorie || '')}</span></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="editService(${service.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteService(${service.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="7">Aucun service trouvé</td></tr>';
                }
            } catch (error) {
                console.error('Erreur:', error);
                tbody.innerHTML = '<tr><td colspan="7">Erreur de chargement</td></tr>';
            }
        }

        async function loadClients() {
            const tbody = document.getElementById('clientsTableBody');
            tbody.innerHTML = '<tr><td colspan="8" class="loading"><i class="fas fa-spinner fa-spin"></i> Chargement...</td></tr>';
            
            try {
                const response = await fetch(API_URL + '?section=clients', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    let html = '';
                    result.data.forEach(client => {
                        const statutClass = getClientStatutClass(client.statut);
                        const statutText = getClientStatutText(client.statut);
                        
                        html += `
                            <tr>
                                <td>${client.id}</td>
                                <td>${escapeHtml(client.nom_entreprise)}</td>
                                <td>${escapeHtml(client.contact_nom)} ${escapeHtml(client.contact_prenom || '')}</td>
                                <td>${escapeHtml(client.email)}</td>
                                <td>${escapeHtml(client.telephone || '')}</td>
                                <td>${escapeHtml(client.ville || '')}</td>
                                <td><span class="badge ${statutClass}">${statutText}</span></td>
                                <td>${formatDate(client.date_inscription)}</td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="8">Aucun client trouvé</td></tr>';
                }
            } catch (error) {
                console.error('Erreur:', error);
                tbody.innerHTML = '<tr><td colspan="8">Erreur de chargement</td></tr>';
            }
        }

        async function loadAlertes() {
            const content = document.getElementById('alertesContent');
            content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';
            
            try {
                const response = await fetch(API_URL + '?section=demandes', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    // Filtrer les demandes non traitées
                    const alertes = result.data.filter(d => d.statut === 'nouvelle');
                    
                    if (alertes.length === 0) {
                        content.innerHTML = '<div class="stat-card"><i class="fas fa-check-circle" style="color: var(--green); font-size: 48px; margin-bottom: 20px;"></i><h3>Aucune alerte</h3><p>Toutes les demandes sont traitées</p></div>';
                    } else {
                        let html = `
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-value">${alertes.length}</div>
                                    <div class="stat-label">Demandes urgentes</div>
                                </div>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Service</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        
                        alertes.forEach(demande => {
                            html += `
                                <tr>
                                    <td>${demande.id}</td>
                                    <td>${escapeHtml(demande.nom)}</td>
                                    <td>${escapeHtml(demande.service_nom || 'N/A')}</td>
                                    <td>${formatDate(demande.date_demande)}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="editDemande(${demande.id})">
                                            <i class="fas fa-eye"></i> Voir
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        html += '</tbody></table>';
                        content.innerHTML = html;
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                content.innerHTML = '<div class="loading">Erreur de chargement</div>';
            }
        }

        async function loadStatistiques() {
            const content = document.getElementById('statsContent');
            content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';
            
            try {
                const response = await fetch(API_URL + '?section=statistiques', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    const stats = result.data;
                    
                    let html = `
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-value">${stats.total_demandes}</div>
                                <div class="stat-label">Total demandes</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">${stats.demandes_recentes}</div>
                                <div class="stat-label">7 derniers jours</div>
                            </div>
                        </div>
                        
                        <h3 style="margin-top: 30px; margin-bottom: 20px;">Demandes par statut</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Statut</th>
                                    <th>Nombre</th>
                                    <th>Pourcentage</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    stats.demandes_par_statut.forEach(stat => {
                        const pourcentage = stats.total_demandes > 0 ? ((stat.count / stats.total_demandes) * 100).toFixed(1) : '0';
                        const statutClass = getStatutClass(stat.statut);
                        const statutText = getStatutText(stat.statut);
                        
                        html += `
                            <tr>
                                <td><span class="badge ${statutClass}">${statutText}</span></td>
                                <td>${stat.count}</td>
                                <td>${pourcentage}%</td>
                            </tr>
                        `;
                    });
                    
                    html += `
                            </tbody>
                        </table>
                        
                        <h3 style="margin-top: 30px; margin-bottom: 20px;">Services les plus demandés</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Demandes</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    stats.services_populaires.forEach(service => {
                        html += `
                            <tr>
                                <td>${escapeHtml(service.nom)}</td>
                                <td>${service.count}</td>
                            </tr>
                        `;
                    });
                    
                    html += `
                            </tbody>
                        </table>
                        
                        <p style="margin-top: 20px; color: var(--gray); font-size: 14px;">
                            <i class="fas fa-clock"></i> Dernière mise à jour : ${stats.timestamp}
                        </p>
                    `;
                    
                    content.innerHTML = html;
                }
            } catch (error) {
                console.error('Erreur:', error);
                content.innerHTML = '<div class="loading">Erreur de chargement</div>';
            }
        }

        // ========== FONCTIONS DE RECHERCHE ==========
        
        async function searchUsers(query) {
            if (query.length < 2 && query.length > 0) return;
            
            const tbody = document.getElementById('usersTableBody');
            
            if (!query) {
                loadUtilisateurs();
                return;
            }
            
            // Filtrage côté client (simplifié)
            try {
                const response = await fetch(API_URL + '?section=utilisateurs', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    const filtered = result.data.filter(user => 
                        user.nom.toLowerCase().includes(query.toLowerCase()) ||
                        user.email.toLowerCase().includes(query.toLowerCase()) ||
                        user.role.toLowerCase().includes(query.toLowerCase())
                    );
                    
                    let html = '';
                    filtered.forEach(user => {
                        const isCurrentUser = currentAdmin && user.id == currentAdmin.id;
                        html += `
                            <tr>
                                <td>${user.id}</td>
                                <td>${escapeHtml(user.nom)} ${isCurrentUser ? '<span class="badge badge-info">(Vous)</span>' : ''}</td>
                                <td>${escapeHtml(user.email)}</td>
                                <td><span class="badge ${user.role === 'admin' ? 'badge-danger' : 'badge-secondary'}">${user.role}</span></td>
                                <td>${formatDate(user.date_creation)}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="editAdmin(${user.id})" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    ${!isCurrentUser ? `
                                        <button class="btn btn-danger btn-sm" onclick="deleteAdmin(${user.id})" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    ` : ''}
                                </td>
                            </tr>
                        `;
                    });
                    
                    if (html === '') {
                        html = '<tr><td colspan="6">Aucun résultat trouvé</td></tr>';
                    }
                    
                    tbody.innerHTML = html;
                }
            } catch (error) {
                console.error('Erreur recherche:', error);
            }
        }

        async function searchDemandes(query) {
            if (query.length < 2 && query.length > 0) return;
            
            const tbody = document.getElementById('demandesTableBody');
            
            if (!query) {
                loadDemandes();
                return;
            }
            
            try {
                const response = await fetch(API_URL + '?search=' + encodeURIComponent(query), {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    let html = '';
                    result.data.forEach(demande => {
                        const statutClass = getStatutClass(demande.statut);
                        const statutText = getStatutText(demande.statut);
                        
                        html += `
                            <tr>
                                <td>${demande.id}</td>
                                <td>${escapeHtml(demande.nom)}</td>
                                <td>${escapeHtml(demande.service_nom || 'N/A')}</td>
                                <td>${escapeHtml(demande.email)}</td>
                                <td>${escapeHtml(demande.telephone)}</td>
                                <td>${formatDate(demande.date_demande)}</td>
                                <td><span class="badge ${statutClass}">${statutText}</span></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="editDemande(${demande.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteDemande(${demande.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    if (html === '') {
                        html = '<tr><td colspan="8">Aucun résultat trouvé</td></tr>';
                    }
                    
                    tbody.innerHTML = html;
                }
            } catch (error) {
                console.error('Erreur recherche:', error);
            }
        }

        async function searchClients(query) {
            if (query.length < 2 && query.length > 0) return;
            
            const tbody = document.getElementById('clientsTableBody');
            
            if (!query) {
                loadClients();
                return;
            }
            
            // Filtrage côté client (simplifié)
            try {
                const response = await fetch(API_URL + '?section=clients', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    const filtered = result.data.filter(client => 
                        client.nom_entreprise.toLowerCase().includes(query.toLowerCase()) ||
                        client.contact_nom.toLowerCase().includes(query.toLowerCase()) ||
                        client.email.toLowerCase().includes(query.toLowerCase()) ||
                        client.ville.toLowerCase().includes(query.toLowerCase())
                    );
                    
                    let html = '';
                    filtered.forEach(client => {
                        const statutClass = getClientStatutClass(client.statut);
                        const statutText = getClientStatutText(client.statut);
                        
                        html += `
                            <tr>
                                <td>${client.id}</td>
                                <td>${escapeHtml(client.nom_entreprise)}</td>
                                <td>${escapeHtml(client.contact_nom)} ${escapeHtml(client.contact_prenom || '')}</td>
                                <td>${escapeHtml(client.email)}</td>
                                <td>${escapeHtml(client.telephone || '')}</td>
                                <td>${escapeHtml(client.ville || '')}</td>
                                <td><span class="badge ${statutClass}">${statutText}</span></td>
                                <td>${formatDate(client.date_inscription)}</td>
                            </tr>
                        `;
                    });
                    
                    if (html === '') {
                        html = '<tr><td colspan="8">Aucun résultat trouvé</td></tr>';
                    }
                    
                    tbody.innerHTML = html;
                }
            } catch (error) {
                console.error('Erreur recherche:', error);
            }
        }

        // ========== FONCTIONS GESTION ADMINS ==========

        function openAddAdminModal() {
            const formContent = `
                <div class="form-group">
                    <label>Nom complet *</label>
                    <input type="text" id="addAdminNom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" id="addAdminEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe *</label>
                    <input type="password" id="addAdminPassword" class="form-control" required>
                    <small style="color: var(--gray);">Minimum 6 caractères</small>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe *</label>
                    <input type="password" id="addAdminConfirmPassword" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Rôle</label>
                    <select id="addAdminRole" class="form-control">
                        <option value="moderateur">Modérateur</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
            `;
            
            document.getElementById('editModalTitle').innerHTML = '<i class="fas fa-user-plus"></i> Ajouter un administrateur';
            document.getElementById('editFormContent').innerHTML = formContent;
            document.getElementById('editModal').style.display = 'flex';
            
            document.getElementById('editForm').onsubmit = async function(e) {
                e.preventDefault();
                
                const nom = document.getElementById('addAdminNom').value;
                const email = document.getElementById('addAdminEmail').value;
                const password = document.getElementById('addAdminPassword').value;
                const confirmPassword = document.getElementById('addAdminConfirmPassword').value;
                const role = document.getElementById('addAdminRole').value;
                
                if (password !== confirmPassword) {
                    alert('Les mots de passe ne correspondent pas');
                    return;
                }
                
                if (password.length < 6) {
                    alert('Le mot de passe doit avoir au moins 6 caractères');
                    return;
                }
                
                try {
                    const response = await fetch(API_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + currentToken
                        },
                        body: JSON.stringify({
                            action: 'add_admin',
                            nom: nom,
                            email: email,
                            password: password,
                            role: role
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Administrateur ajouté avec succès');
                        closeModal('editModal');
                        loadUtilisateurs();
                        
                        // Google Analytics
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'add_admin', { role: role });
                        }
                    } else {
                        alert('Erreur: ' + (result.error || 'Échec de l\'ajout'));
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur d\'ajout');
                }
            };
        }

        async function editAdmin(id) {
            try {
                const response = await fetch(API_URL + '?section=utilisateurs', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    const admin = result.data.find(u => u.id == id);
                    
                    if (admin) {
                        const isCurrentUser = currentAdmin && admin.id == currentAdmin.id;
                        const formContent = `
                            <div class="form-group">
                                <label>Nom complet *</label>
                                <input type="text" id="editAdminNom" class="form-control" value="${escapeHtml(admin.nom)}" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" id="editAdminEmail" class="form-control" value="${escapeHtml(admin.email)}" required>
                            </div>
                            <div class="form-group">
                                <label>Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                                <input type="password" id="editAdminPassword" class="form-control">
                                ${isCurrentUser ? '<small style="color: var(--gray);">Vous devrez vous reconnecter si vous changez le mot de passe</small>' : ''}
                            </div>
                            ${!isCurrentUser ? `
                            <div class="form-group">
                                <label>Rôle</label>
                                <select id="editAdminRole" class="form-control">
                                    <option value="moderateur" ${admin.role === 'moderateur' ? 'selected' : ''}>Modérateur</option>
                                    <option value="admin" ${admin.role === 'admin' ? 'selected' : ''}>Administrateur</option>
                                </select>
                            </div>
                            ` : ''}
                            <input type="hidden" id="editAdminId" value="${admin.id}">
                        `;
                        
                        document.getElementById('editModalTitle').innerHTML = '<i class="fas fa-user-edit"></i> Modifier l\'administrateur';
                        document.getElementById('editFormContent').innerHTML = formContent;
                        document.getElementById('editModal').style.display = 'flex';
                        
                        document.getElementById('editForm').onsubmit = async function(e) {
                            e.preventDefault();
                            
                            const nom = document.getElementById('editAdminNom').value;
                            const email = document.getElementById('editAdminEmail').value;
                            const password = document.getElementById('editAdminPassword').value;
                            const role = !isCurrentUser ? document.getElementById('editAdminRole').value : admin.role;
                            const adminId = document.getElementById('editAdminId').value;
                            
                            if (password && password.length < 6) {
                                alert('Le mot de passe doit avoir au moins 6 caractères');
                                return;
                            }
                            
                            try {
                                const response = await fetch(API_URL, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Authorization': 'Bearer ' + currentToken
                                    },
                                    body: JSON.stringify({
                                        action: 'update_admin',
                                        id: adminId,
                                        nom: nom,
                                        email: email,
                                        password: password || null,
                                        role: role
                                    })
                                });
                                
                                const result = await response.json();
                                
                                if (result.success) {
                                    alert('Administrateur mis à jour avec succès');
                                    closeModal('editModal');
                                    loadUtilisateurs();
                                    
                                    // Si c'est l'admin actuel qui se modifie, mettre à jour le nom affiché
                                    if (isCurrentUser) {
                                        currentAdmin.nom = nom;
                                        currentAdmin.email = email;
                                        localStorage.setItem('adminData', JSON.stringify(currentAdmin));
                                        document.getElementById('userName').textContent = nom;
                                        
                                        // Si le mot de passe a été changé, forcer la reconnexion
                                        if (password) {
                                            alert('Votre mot de passe a été changé. Veuillez vous reconnecter.');
                                            logout();
                                        }
                                    }
                                    
                                    // Google Analytics
                                    if (typeof gtag !== 'undefined') {
                                        gtag('event', 'update_admin', { role: role });
                                    }
                                } else {
                                    alert('Erreur: ' + (result.error || 'Échec de la mise à jour'));
                                }
                            } catch (error) {
                                console.error('Erreur:', error);
                                alert('Erreur de mise à jour');
                            }
                        };
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur de chargement');
            }
        }

        async function deleteAdmin(id) {
            // Empêcher la suppression de soi-même
            if (currentAdmin && id == currentAdmin.id) {
                alert('Vous ne pouvez pas supprimer votre propre compte');
                return;
            }
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ? Cette action est irréversible.')) {
                try {
                    const response = await fetch(API_URL + '?type=admin&id=' + id, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + currentToken
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Administrateur supprimé avec succès');
                        loadUtilisateurs();
                        
                        // Google Analytics
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'delete_admin');
                        }
                    } else {
                        alert('Erreur: ' + (result.error || 'Échec de la suppression'));
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur de suppression');
                }
            }
        }

        // ========== FONCTIONS GESTION DEMANDES ==========
        
        async function editDemande(id) {
            // Charger la demande
            try {
                const response = await fetch(API_URL + '?section=demandes', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    const demande = result.data.find(d => d.id == id);
                    
                    if (demande) {
                        const formContent = `
                            <div class="form-group">
                                <label>Statut</label>
                                <select id="editStatut" class="form-control">
                                    <option value="nouvelle" ${demande.statut === 'nouvelle' ? 'selected' : ''}>Nouvelle</option>
                                    <option value="en_cours" ${demande.statut === 'en_cours' ? 'selected' : ''}>En cours</option>
                                    <option value="traitee" ${demande.statut === 'traitee' ? 'selected' : ''}>Traitée</option>
                                    <option value="annulee" ${demande.statut === 'annulee' ? 'selected' : ''}>Annulée</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Notes (optionnel)</label>
                                <textarea id="editNotes" class="form-control" rows="4">${escapeHtml(demande.notes || '')}</textarea>
                            </div>
                            <input type="hidden" id="editId" value="${demande.id}">
                        `;
                        
                        document.getElementById('editModalTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier la demande #' + demande.id;
                        document.getElementById('editFormContent').innerHTML = formContent;
                        document.getElementById('editModal').style.display = 'flex';
                        
                        // Configurer le formulaire
                        document.getElementById('editForm').onsubmit = async function(e) {
                            e.preventDefault();
                            
                            const statut = document.getElementById('editStatut').value;
                            const notes = document.getElementById('editNotes').value;
                            const demandeId = document.getElementById('editId').value;
                            
                            try {
                                const response = await fetch(API_URL, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Authorization': 'Bearer ' + currentToken
                                    },
                                    body: JSON.stringify({
                                        action: 'update_demande',
                                        id: demandeId,
                                        statut: statut,
                                        notes: notes
                                    })
                                });
                                
                                const result = await response.json();
                                
                                if (result.success) {
                                    alert('Demande mise à jour avec succès');
                                    closeModal('editModal');
                                    loadDemandes();
                                    
                                    // Google Analytics
                                    if (typeof gtag !== 'undefined') {
                                        gtag('event', 'update_demande', { statut: statut });
                                    }
                                } else {
                                    alert('Erreur: ' + (result.error || 'Échec de la mise à jour'));
                                }
                            } catch (error) {
                                console.error('Erreur:', error);
                                alert('Erreur de mise à jour');
                            }
                        };
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur de chargement');
            }
        }

        async function deleteDemande(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')) {
                try {
                    const response = await fetch(API_URL + '?type=demande&id=' + id, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + currentToken
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Demande supprimée avec succès');
                        loadDemandes();
                        
                        // Google Analytics
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'delete_demande');
                        }
                    } else {
                        alert('Erreur: ' + (result.error || 'Échec de la suppression'));
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur de suppression');
                }
            }
        }

        // ========== FONCTIONS GESTION SERVICES ==========
        
        async function editService(id) {
            // Charger le service
            try {
                const response = await fetch(API_URL + '?section=services', {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    const service = result.data.find(s => s.id == id);
                    
                    if (service) {
                        const formContent = `
                            <div class="form-group">
                                <label>Nom du service</label>
                                <input type="text" id="editNom" class="form-control" value="${escapeHtml(service.nom)}" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea id="editDescription" class="form-control" rows="3">${escapeHtml(service.description || '')}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Prix (€)</label>
                                <input type="number" id="editPrix" class="form-control" step="0.01" value="${parseFloat(service.prix)}" required>
                            </div>
                            <div class="form-group">
                                <label>Durée estimée</label>
                                <input type="text" id="editDuree" class="form-control" value="${escapeHtml(service.duree_estimee || '')}">
                            </div>
                            <div class="form-group">
                                <label>Catégorie</label>
                                <input type="text" id="editCategorie" class="form-control" value="${escapeHtml(service.categorie || '')}">
                            </div>
                            <div class="form-group">
                                <label>Icône FontAwesome</label>
                                <input type="text" id="editIcon" class="form-control" value="${escapeHtml(service.icon || 'fa-cog')}">
                                <small style="color: var(--gray);">Ex: fa-server, fa-shield-alt, fa-headset</small>
                            </div>
                            <input type="hidden" id="editId" value="${service.id}">
                        `;
                        
                        document.getElementById('editModalTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier le service';
                        document.getElementById('editFormContent').innerHTML = formContent;
                        document.getElementById('editModal').style.display = 'flex';
                        
                        // Configurer le formulaire
                        document.getElementById('editForm').onsubmit = async function(e) {
                            e.preventDefault();
                            
                            const nom = document.getElementById('editNom').value;
                            const description = document.getElementById('editDescription').value;
                            const prix = document.getElementById('editPrix').value;
                            const duree = document.getElementById('editDuree').value;
                            const categorie = document.getElementById('editCategorie').value;
                            const icon = document.getElementById('editIcon').value;
                            const serviceId = document.getElementById('editId').value;
                            
                            try {
                                const response = await fetch(API_URL, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Authorization': 'Bearer ' + currentToken
                                    },
                                    body: JSON.stringify({
                                        action: 'update_service',
                                        id: serviceId,
                                        nom: nom,
                                        description: description,
                                        prix: prix,
                                        duree_estimee: duree,
                                        categorie: categorie,
                                        icon: icon
                                    })
                                });
                                
                                const result = await response.json();
                                
                                if (result.success) {
                                    alert('Service mis à jour avec succès');
                                    closeModal('editModal');
                                    loadServices();
                                    
                                    // Google Analytics
                                    if (typeof gtag !== 'undefined') {
                                        gtag('event', 'update_service');
                                    }
                                } else {
                                    alert('Erreur: ' + (result.error || 'Échec de la mise à jour'));
                                }
                            } catch (error) {
                                console.error('Erreur:', error);
                                alert('Erreur de mise à jour');
                            }
                        };
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur de chargement');
            }
        }

        async function deleteService(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) {
                try {
                    const response = await fetch(API_URL + '?type=service&id=' + id, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + currentToken
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Service supprimé avec succès');
                        loadServices();
                        
                        // Google Analytics
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'delete_service');
                        }
                    } else {
                        alert('Erreur: ' + (result.error || 'Ce service est utilisé dans des demandes'));
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur de suppression');
                }
            }
        }

        function openAddServiceModal() {
            const formContent = `
                <div class="form-group">
                    <label>Nom du service *</label>
                    <input type="text" id="addNom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="addDescription" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Prix (€) *</label>
                    <input type="number" id="addPrix" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Durée estimée</label>
                    <input type="text" id="addDuree" class="form-control" placeholder="Ex: 1-2 semaines">
                </div>
                <div class="form-group">
                    <label>Catégorie</label>
                    <input type="text" id="addCategorie" class="form-control" placeholder="Ex: infrastructure, sécurité">
                </div>
                <div class="form-group">
                    <label>Icône FontAwesome</label>
                    <input type="text" id="addIcon" class="form-control" value="fa-cog">
                    <small style="color: var(--gray);">Ex: fa-server, fa-shield-alt, fa-headset</small>
                </div>
            `;
            
            document.getElementById('editModalTitle').innerHTML = '<i class="fas fa-plus"></i> Ajouter un service';
            document.getElementById('editFormContent').innerHTML = formContent;
            document.getElementById('editModal').style.display = 'flex';
            
            // Configurer le formulaire
            document.getElementById('editForm').onsubmit = async function(e) {
                e.preventDefault();
                
                const nom = document.getElementById('addNom').value;
                const description = document.getElementById('addDescription').value;
                const prix = document.getElementById('addPrix').value;
                const duree = document.getElementById('addDuree').value;
                const categorie = document.getElementById('addCategorie').value;
                const icon = document.getElementById('addIcon').value;
                
                try {
                    const response = await fetch(API_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + currentToken
                        },
                        body: JSON.stringify({
                            action: 'add_service',
                            nom: nom,
                            description: description,
                            prix: prix,
                            duree_estimee: duree,
                            categorie: categorie,
                            icon: icon
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Service ajouté avec succès');
                        closeModal('editModal');
                        loadServices();
                        
                        // Google Analytics
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'add_service');
                        }
                    } else {
                        alert('Erreur: ' + (result.error || 'Échec de l\'ajout'));
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur d\'ajout');
                }
            };
        }

        async function handleEditForm(e) {
            e.preventDefault();
            // La logique est définie dans chaque fonction d'édition
        }

        function refreshStats() {
            loadStatistiques();
        }

        // ========== FONCTIONS UTILITAIRES ==========
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function getStatutClass(statut) {
            switch(statut) {
                case 'nouvelle': return 'badge-warning';
                case 'en_cours': return 'badge-info';
                case 'traitee': return 'badge-success';
                case 'annulee': return 'badge-danger';
                default: return 'badge-secondary';
            }
        }

        function getStatutText(statut) {
            switch(statut) {
                case 'nouvelle': return 'Nouvelle';
                case 'en_cours': return 'En cours';
                case 'traitee': return 'Traitée';
                case 'annulee': return 'Annulée';
                default: return statut;
            }
        }

        function getClientStatutClass(statut) {
            switch(statut) {
                case 'actif': return 'badge-success';
                case 'inactif': return 'badge-danger';
                case 'prospect': return 'badge-warning';
                default: return 'badge-secondary';
            }
        }

        function getClientStatutText(statut) {
            switch(statut) {
                case 'actif': return 'Actif';
                case 'inactif': return 'Inactif';
                case 'prospect': return 'Prospect';
                default: return statut;
            }
        }

        // Initialiser le premier chargement
        setTimeout(() => {
            if (currentToken) {
                loadDashboardData();
            }
        }, 100);
    </script>
</body>
</html>