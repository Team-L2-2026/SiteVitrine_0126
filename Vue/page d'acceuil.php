<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechSARL Pro - Services Informatiques</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Variables CSS */
        :root {
            --primary: #2563eb;
            --secondary: #1e40af;
            --accent: #3b82f6;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --success: #10b981;
            --transition: all 0.3s ease;
        }

        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        section {
            padding: 80px 0;
        }

        /* Header & Navigation */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            color: var(--accent);
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 600;
            font-size: 16px;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--primary);
            left: 0;
            bottom: -5px;
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .mobile-menu {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 160px 0 100px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path d="M0,0v100h1000V0C1000,0,0,0,0,0z" fill="white"/></svg>') bottom center no-repeat;
            background-size: 100% 50px;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 800;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .cta-button {
            display: inline-block;
            background: white;
            color: var(--primary);
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            font-size: 18px;
            transition: var(--transition);
            border: 2px solid white;
        }

        .cta-button:hover {
            background: transparent;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Services Section */
        .services-section {
            background: white;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
            color: var(--dark);
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--primary);
            margin: 15px auto;
            border-radius: 2px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .service-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 32px;
            color: white;
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--dark);
        }

        /* Contact Section */
        .contact-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }

        .contact-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 50px;
        }

        .contact-info h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .contact-details {
            margin-top: 30px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .contact-item i {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Formulaire */
        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 16px 40px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }
/* RÉSEAUX SOCIAUX */
.socials{
    text-align:center;
    margin-top:20px;
}
.socials a{
    margin:10px;
    font-size:22px;
    text-decoration:none;
    color:#002b5b;
}
        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-logo {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: white;
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .mobile-menu {
                display: block;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .contact-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header & Navigation -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <i class="fas fa-laptop-code"></i>
                    TechSARL Pro
                </div>
                <div class="nav-links">
                    <a href="#accueil">Accueil</a>
                    <a href="#services">Services</a>
                    <a href="#contact">Contact</a>
                    <a href="#formulaire" class="cta-button">Demander un devis</a>
                </div>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="accueil" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Solutions Informatiques Professionnelles</h1>
                <p>Nous transformons votre infrastructure IT avec des solutions sur mesure, une maintenance proactive et un support 24/7. L'excellence technique au service de votre entreprise.</p>
                <a href="#services" class="cta-button">Découvrir nos services</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <h2 class="section-title">Nos Services Expert</h2>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-server"></i>
                    </div>
                    <h3>Infrastructure & Cloud</h3>
                    <p>Déploiement et gestion de serveurs, virtualisation, migration cloud (AWS, Azure, Google Cloud), et optimisation des performances.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Cybersécurité</h3>
                    <p>Audits de sécurité, protection des données, firewall avancés, formation des employés et surveillance continue des menaces.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Support Technique 24/7</h3>
                    <p>Assistance technique à distance, gestion des incidents, maintenance préventive et dépannage urgent pour votre entreprise.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3>Sauvegarde & Récupération</h3>
                    <p>Solutions de sauvegarde automatisée, plan de reprise d'activité (PRA) et tests réguliers de restauration de données.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <h3>Réseau & Télécom</h3>
                    <p>Conception, installation et optimisation de réseaux locaux (LAN/WLAN), VoIP, et solutions de communication unifiée.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3>Développement web Sur Mesure</h3>
                    <p>Applications métiers, sites web professionnels, automatisation de processus et intégration de systèmes existants.</p>
                </div>
            </div>
        </div>

    </section>

    <!-- Contact & Formulaire -->
    <section id="contact" class="contact-section">
        <div class="container">
            <h2 class="section-title">Contactez-nous</h2>
            <div class="contact-container">
                <div class="contact-info">
                    <h3>Parlons de votre projet</h3>
                    <p>Notre équipe d'experts est prête à analyser vos besoins et à vous proposer la solution informatique la plus adaptée à votre entreprise.</p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Adresse</strong>
                                <p>123 Avenue de la Technologie, 75000 Paris</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong>Téléphone</strong>
                                <p>01 23 45 67 89</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email</strong>
                                <p>contact@techsolutions-pro.fr</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Horaires</strong>
                                <p>Lun-Ven: 8h-19h | Urgences: 24/7</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de contact -->
                <div id="formulaire" class="contact-form">
                    <h3 style="margin-bottom: 30px;">Demande de devis gratuit</h3>
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="name">Nom complet *</label>
                            <input type="text" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="company">Entreprise</label>
                            <input type="text" id="company" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Téléphone *</label>
                            <input type="tel" id="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="service">Service intéressé</label>
                            <select id="service" class="form-control">
                                <option value="">Sélectionnez un service</option>
                                <option value="infrastructure">Infrastructure & Cloud</option>
                                <option value="security">Cybersécurité</option>
                                <option value="support">Support Technique</option>
                                <option value="backup">Sauvegarde & Récupération</option>
                                <option value="network">Réseau & Télécom</option>
                                <option value="development">Développement</option>
                                <option value="multiple">Plusieurs services</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Description de vos besoins *</label>
                            <textarea id="message" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Envoyer la demande
                        </button>
                        <div id="formMessage" style="margin-top: 20px; display: none;"></div>
                    </form>
                </div>
            </div>
        </div>
        <!-- RÉSEAUX SOCIAUX -->
<div class="socials">
<a href="https://facebook.com" target="_blank">🌐 Facebook</a>
<a href="https://linkedin.com" target="_blank">💼 LinkedIn</a>
<a href="https://twitter.com" target="_blank">🐦 Twitter</a>
<a href="https://instagram.com" target="_blank">📸 Instagram</a>
</div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div>
                    <div class="footer-logo">
                        <i class="fas fa-laptop-code"></i>
                        TechSolutions Pro
                    </div>
                    <p>Votre partenaire informatique de confiance pour la transformation digitale de votre entreprise.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i cl