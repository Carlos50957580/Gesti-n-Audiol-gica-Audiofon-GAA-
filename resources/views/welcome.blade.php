<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plataforma de Gestión de Carreras UASD</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            color: #333;
        }
        
        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.2rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            padding: 0.8rem 0;
            box-shadow: 0 6px 30px rgba(0,0,0,0.15);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-brand i {
            font-size: 2rem;
        }
        
        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            margin: 0 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .navbar-nav .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }
        
        .btn-login {
            background: white;
            color: #667eea;
            border: none;
            padding: 10px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255,255,255,0.2);
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255,255,255,0.3);
            color: #667eea;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 180px 0 120px;
            color: white;
            position: relative;
            overflow: hidden;
            margin-top: 0;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 25px;
            line-height: 1.2;
            animation: fadeInUp 1s ease;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.95;
            animation: fadeInUp 1s ease 0.2s both;
        }
        
        .hero-buttons {
            animation: fadeInUp 1s ease 0.4s both;
        }
        
        .btn-hero {
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
            margin: 10px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary-hero {
            background: white;
            color: #667eea;
            box-shadow: 0 10px 30px rgba(255,255,255,0.3);
        }
        
        .btn-primary-hero:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(255,255,255,0.4);
            color: #667eea;
        }
        
        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-outline-hero:hover {
            background: white;
            color: #667eea;
            transform: translateY(-5px);
        }
        
        .hero-image {
            animation: float 3s ease-in-out infinite;
            position: relative;
            z-index: 2;
        }
        
        .hero-image img {
            width: 100%;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
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
        
        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: #f8f9fa;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #2d3748;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            color: #718096;
            margin-bottom: 60px;
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .feature-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2d3748;
        }
        
        .feature-text {
            color: #718096;
            line-height: 1.8;
        }
        
        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 80px 0;
            color: white;
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
        }
        
        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            display: block;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        /* Process Section */
        .process-section {
            padding: 100px 0;
            background: white;
        }
        
        .process-step {
            position: relative;
            padding-left: 100px;
            margin-bottom: 50px;
        }
        
        .step-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .step-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2d3748;
        }
        
        .step-description {
            color: #718096;
            line-height: 1.8;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 100px 0;
            color: white;
            text-align: center;
        }
        
        .cta-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 25px;
        }
        
        .cta-text {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.95;
        }
        
        /* Footer */
        .footer {
            background: #2d3748;
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 25px;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }
        
        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            color: white;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
        }
        
        .copyright {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            color: rgba(255,255,255,0.7);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .cta-title {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i>
                UASD Carreras
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars text-white"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#funcionalidades">Funcionalidades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#proceso">Proceso</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a href="/login" class="btn btn-login">Iniciar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="inicio">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">Gestión Inteligente de Planes de Estudio</h1>
                    <p class="hero-subtitle">Plataforma virtual para el diseño, evaluación y aprobación de programas académicos en la Universidad Autónoma de Santo Domingo</p>
                    <div class="hero-buttons">
                        <a href="/register" class="btn btn-hero btn-primary-hero">Comenzar Ahora</a>
                        <a href="#funcionalidades" class="btn btn-hero btn-outline-hero">Conocer Más</a>
                    </div>
                </div>
                <div class="col-lg-6 hero-image">
                    <svg viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:rgba(255,255,255,0.9);stop-opacity:1" />
                                <stop offset="100%" style="stop-color:rgba(255,255,255,0.7);stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <circle cx="250" cy="250" r="200" fill="url(#grad1)" opacity="0.3"/>
                        <rect x="150" y="150" width="200" height="250" rx="10" fill="url(#grad1)"/>
                        <rect x="170" y="180" width="50" height="50" rx="5" fill="white" opacity="0.8"/>
                        <rect x="230" y="180" width="90" height="15" rx="3" fill="white" opacity="0.8"/>
                        <rect x="230" y="205" width="70" height="10" rx="2" fill="white" opacity="0.6"/>
                        <rect x="170" y="250" width="160" height="10" rx="2" fill="white" opacity="0.7"/>
                        <rect x="170" y="270" width="140" height="10" rx="2" fill="white" opacity="0.7"/>
                        <rect x="170" y="290" width="160" height="10" rx="2" fill="white" opacity="0.7"/>
                        <rect x="170" y="320" width="160" height="30" rx="5" fill="white" opacity="0.9"/>
                        <circle cx="450" cy="100" r="30" fill="white" opacity="0.2"/>
                        <circle cx="80" cy="400" r="40" fill="white" opacity="0.2"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <span class="stat-number"><i class="fas fa-university"></i> 50+</span>
                        <span class="stat-label">Facultades Activas</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <span class="stat-number"><i class="fas fa-book"></i> 200+</span>
                        <span class="stat-label">Planes de Estudio</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <span class="stat-number"><i class="fas fa-users"></i> 1000+</span>
                        <span class="stat-label">Usuarios Registrados</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <span class="stat-number"><i class="fas fa-clock"></i> 24/7</span>
                        <span class="stat-label">Disponibilidad</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="funcionalidades">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">Funcionalidades Principales</h2>
                <p class="section-subtitle">Herramientas poderosas para optimizar la gestión académica</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="feature-title">Gestión de Planes</h3>
                        <p class="feature-text">Crea, edita y gestiona planes de estudio de manera intuitiva con formularios virtuales completamente digitalizados.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h3 class="feature-title">Evaluación Automatizada</h3>
                        <p class="feature-text">Sistema de evaluación integral que permite a los evaluadores revisar propuestas de forma eficiente y estructurada.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Reportes Avanzados</h3>
                        <p class="feature-text">Genera informes preliminares y ejecutivos con datos estadísticos para la toma de decisiones estratégicas.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3 class="feature-title">Control de Usuarios</h3>
                        <p class="feature-text">Administra roles y permisos para universidades, evaluadores y autoridades de forma centralizada.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h3 class="feature-title">Carga Masiva</h3>
                        <p class="feature-text">Importa datos desde plantillas Excel para agilizar el ingreso de asignaturas y personal docente.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3 class="feature-title">Notificaciones</h3>
                        <p class="feature-text">Sistema de alertas en tiempo real para mantener informados a todos los participantes del proceso.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section" id="proceso">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">¿Cómo Funciona?</h2>
                <p class="section-subtitle">Proceso simplificado en 4 pasos</p>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <h3 class="step-title">Registro y Configuración</h3>
                        <p class="step-description">Crea tu cuenta institucional, configura tu perfil y define la estructura organizacional de tu facultad o departamento.</p>
                    </div>
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <h3 class="step-title">Creación del Plan</h3>
                        <p class="step-description">Completa los formularios virtuales con la información del plan de estudio: asignaturas, créditos, personal docente y competencias.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <h3 class="step-title">Evaluación y Seguimiento</h3>
                        <p class="step-description">Somete tu propuesta a evaluación. Recibe notificaciones y observaciones en tiempo real durante todo el proceso.</p>
                    </div>
                    <div class="process-step">
                        <div class="step-number">4</div>
                        <h3 class="step-title">Aprobación y Reportes</h3>
                        <p class="step-description">Una vez aprobado, accede a reportes detallados y documentación oficial para la implementación del programa.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="contacto">
        <div class="container">
            <h2 class="cta-title">¿Listo para Digitalizar tus Procesos Académicos?</h2>
            <p class="cta-text">Únete a las instituciones que ya confían en nuestra plataforma</p>
            <div>
                <a href="/register" class="btn btn-hero btn-primary-hero me-2">Crear Cuenta Gratis</a>
                <a href="mailto:soporte@uasd.edu.do" class="btn btn-hero btn-outline-hero">Contactar Soporte</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h3 class="footer-title">
                        <i class="fas fa-graduation-cap me-2"></i>
                        UASD Carreras
                    </h3>
                    <p>Plataforma oficial para la gestión de planes de estudio de la Universidad Autónoma de Santo Domingo.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h4 class="footer-title">Enlaces</h4>
                    <ul class="footer-links">
                        <li><a href="#inicio">Inicio</a></li>
                        <li><a href="#funcionalidades">Funcionalidades</a></li>
                        <li><a href="#proceso">Proceso</a></li>
                        <li><a href="/login">Ingresar</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h4 class="footer-title">Recursos</h4>
                    <ul class="footer-links">
                        <li><a href="#">Documentación</a></li>
                        <li><a href="#">Video Tutoriales</a></li>
                        <li><a href="#">Preguntas Frecuentes</a></li>
                        <li><a href="#">Centro de Ayuda</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h4 class="footer-title">Contacto</h4>
                    <ul class="footer-links">
                        <li><i class="fas fa-phone me-2"></i> +1 (809) 626-1617</li>
                        <li><i class="fas fa-envelope me-2"></i> info@uasd.edu.do</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> Santo Domingo, RD</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 UASD - Universidad Autónoma de Santo Domingo. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>