<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Tuition - Modern Education Management</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --secondary: #06B6D4;
            --accent: #F59E0B;
            --dark: #0F172A;
            --gray: #64748B;
            --light-gray: #F1F5F9;
            --white: #FFFFFF;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Navbar */
        .navbar {
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.2rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
        }
        
        .logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2.5rem;
            align-items: center;
        }
        
        .nav-menu a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.3s;
            position: relative;
        }
        
        .nav-menu a:hover {
            color: var(--primary);
        }
        
        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s;
        }
        
        .nav-menu a:hover::after {
            width: 100%;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 0.7rem 1.8rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--primary);
            padding: 0.7rem 1.8rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            border: 2px solid var(--primary);
            transition: all 0.3s;
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        /* Hero Section */
        .hero {
            padding: 10rem 5% 8rem;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            opacity: 0.1;
            border-radius: 50%;
            top: -200px;
            right: -100px;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-30px); }
        }
        
        .hero-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }
        
        .hero-content h1 span {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-content p {
            font-size: 1.2rem;
            color: var(--gray);
            margin-bottom: 2.5rem;
            line-height: 1.8;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        
        .hero-image {
            position: relative;
        }
        
        .hero-image-main {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }
        
        .hero-image-main img {
            width: 100%;
            border-radius: 12px;
        }
        
        .floating-card {
            position: absolute;
            background: white;
            padding: 1.2rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: float 4s ease-in-out infinite;
            z-index: 10;
        }
        
        .floating-card:nth-child(1) {
            top: 10%;
            left: -5%;
            animation-delay: 0s;
        }
        
        .floating-card:nth-child(3) {
            bottom: 15%;
            right: -5%;
            animation-delay: 2s;
        }
        
        .floating-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .card-icon-blue {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        
        .card-icon-orange {
            background: linear-gradient(135deg, #F59E0B, #EF4444);
            color: white;
        }
        
        /* Stats Section */
        .stats {
            padding: 5rem 5%;
            background: white;
        }
        
        .stats-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3rem;
            text-align: center;
        }
        
        .stat-item h3 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-item p {
            color: var(--gray);
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        /* Features Section */
        .features {
            padding: 8rem 5%;
            background: var(--light-gray);
        }
        
        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 5rem;
        }
        
        .section-label {
            display: inline-block;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .section-header h2 {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .section-header p {
            font-size: 1.2rem;
            color: var(--gray);
        }
        
        .features-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
        }
        
        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            transition: all 0.3s;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(6, 182, 212, 0.1));
        }
        
        .feature-card h3 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .feature-card p {
            color: var(--gray);
            line-height: 1.7;
        }
        
        /* Portal Section */
        .portals {
            padding: 8rem 5%;
            background: white;
        }
        
        .portals-grid {
            max-width: 1200px;
            margin: 4rem auto 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 3rem;
        }
        
        .portal-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 20px;
            padding: 3rem;
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .portal-card::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -50px;
            right: -50px;
        }
        
        .portal-card:hover {
            transform: translateY(-10px);
        }
        
        .portal-card.secondary {
            background: linear-gradient(135deg, var(--secondary), #0891B2);
        }
        
        .portal-icon-large {
            font-size: 60px;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }
        
        .portal-card h3 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }
        
        .portal-card p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .portal-features {
            list-style: none;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 1;
        }
        
        .portal-features li {
            padding: 0.6rem 0;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            opacity: 0.9;
        }
        
        .portal-features li::before {
            content: '✓';
            width: 24px;
            height: 24px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .btn-white {
            background: white;
            color: var(--primary);
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            position: relative;
            z-index: 1;
        }
        
        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .portal-card.secondary .btn-white {
            color: var(--secondary);
        }
        
        /* CTA Section */
        .cta {
            padding: 8rem 5%;
            background: linear-gradient(135deg, var(--dark), #1E293B);
            color: white;
            text-align: center;
        }
        
        .cta-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .cta h2 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }
        
        .cta p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
        }
        
        /* Footer */
        .footer {
            background: var(--dark);
            color: white;
            padding: 4rem 5% 2rem;
        }
        
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .footer-brand p {
            color: rgba(255, 255, 255, 0.7);
            margin-top: 1rem;
            line-height: 1.8;
        }
        
        .footer-links h4 {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: block;
            padding: 0.5rem 0;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--dark);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .hero-content h1 {
                font-size: 2.8rem;
            }
            
            .hero-buttons {
                justify-content: center;
            }
            
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .portals-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .hero {
                padding: 8rem 5% 6rem;
            }
            
            .hero-content h1 {
                font-size: 2.2rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
            }
            
            .section-header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="logo">
                <div class="logo-icon">📚</div>
                <span>Simple Tuition</span>
            </a>
            <ul class="nav-menu">
                <li><a href="#features">Features</a></li>
                <li><a href="#portals">Portals</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="student/login.php">Login</a></li>
                <li><a href="student/register.php" class="btn-primary">Get Started</a></li>
            </ul>
            <button class="mobile-menu-btn">☰</button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Transform Your <span>Education Management</span> Today</h1>
                <p>The complete solution for managing students, classes, payments, and attendance. Streamline your educational institution with our powerful platform.</p>
                <div class="hero-buttons">
                    <a href="student/register.php" class="btn-primary">Start Free Trial</a>
                    <a href="#portals" class="btn-outline">Explore Features</a>
                </div>
            </div>
            <div class="hero-image">
                <div class="floating-card">
                    <div class="floating-card-icon card-icon-blue">📊</div>
                    <div>
                        <div style="font-weight: 600; font-size: 1.1rem;">1,200+</div>
                        <div style="color: var(--gray); font-size: 0.9rem;">Active Students</div>
                    </div>
                </div>
                <div class="hero-image-main">
                    <svg width="100%" height="300" viewBox="0 0 500 300">
                        <rect x="50" y="50" width="400" height="200" rx="10" fill="#EEF2FF"/>
                        <rect x="70" y="80" width="150" height="15" rx="5" fill="#4F46E5"/>
                        <rect x="70" y="110" width="200" height="15" rx="5" fill="#C7D2FE"/>
                        <rect x="70" y="140" width="180" height="15" rx="5" fill="#C7D2FE"/>
                        <circle cx="350" cy="150" r="60" fill="#4F46E5" opacity="0.2"/>
                        <circle cx="350" cy="150" r="45" fill="#4F46E5" opacity="0.4"/>
                        <circle cx="350" cy="150" r="30" fill="#4F46E5"/>
                        <text x="350" y="155" text-anchor="middle" fill="white" font-size="20" font-weight="bold">✓</text>
                    </svg>
                </div>
                <div class="floating-card">
                    <div class="floating-card-icon card-icon-orange">⚡</div>
                    <div>
                        <div style="font-weight: 600; font-size: 1.1rem;">98% Faster</div>
                        <div style="color: var(--gray); font-size: 0.9rem;">Processing Time</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-container">
            <div class="stat-item">
                <h3>100%</h3>
                <p>Free & Open Source</p>
            </div>
            <div class="stat-item">
                <h3>∞</h3>
                <p>Unlimited Students</p>
            </div>
            <div class="stat-item">
                <h3>24/7</h3>
                <p>Access Anytime</p>
            </div>
            <div class="stat-item">
                <h3>5min</h3>
                <p>Quick Setup</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-header">
            <span class="section-label">✨ FEATURES</span>
            <h2>Everything You Need to Succeed</h2>
            <p>Powerful tools designed to make education management effortless</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Student Management</h3>
                <p>Complete student profiles with enrollment tracking, contact information, and academic records organized in one intuitive dashboard.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📚</div>
                <h3>Class Scheduling</h3>
                <p>Create and manage class schedules, assign teachers, and organize subjects with powerful automation tools.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Fee Tracking</h3>
                <p>Monitor payments in real-time, track pending fees, and generate financial reports with complete transparency.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">✅</div>
                <h3>Attendance System</h3>
                <p>Mark attendance with ease, generate reports, and identify patterns to improve student engagement.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Analytics Dashboard</h3>
                <p>Get actionable insights with real-time statistics and comprehensive reports for data-driven decisions.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Secure Platform</h3>
                <p>Bank-level security with encrypted data, secure authentication, and role-based access control.</p>
            </div>
        </div>
    </section>

    <!-- Portals Section -->
    <section class="portals" id="portals">
        <div class="section-header">
            <span class="section-label">🚀 GET STARTED</span>
            <h2>Choose Your Portal</h2>
            <p>Select the portal that matches your role and start managing today</p>
        </div>
        <div class="portals-grid">
            <div class="portal-card">
                <div class="portal-icon-large">👨‍💼</div>
                <h3>Admin Portal</h3>
                <p>Complete control and oversight of your educational institution</p>
                <ul class="portal-features">
                    <li>Full student & staff management</li>
                    <li>Class scheduling & organization</li>
                    <li>Fee tracking & financial reports</li>
                    <li>Attendance monitoring</li>
                    <li>Comprehensive analytics</li>
                </ul>
                <a href="admin/login.php" class="btn-white">Access Admin Portal →</a>
            </div>
            <div class="portal-card secondary">
                <div class="portal-icon-large">🎓</div>
                <h3>Student Portal</h3>
                <p>Your personalized learning and information hub</p>
                <ul class="portal-features">
                    <li>View class schedules & materials</li>
                    <li>Check fee payment status</li>
                    <li>Track attendance records</li>
                    <li>Update profile information</li>
                    <li>24/7 access from anywhere</li>
                </ul>
                <a href="student/login.php" class="btn-white">Student Login →</a>
                <div style="margin-top: 1rem; opacity: 0.9;">
                    <a href="student/register.php" style="color: white; text-decoration: underline;">New student? Register here</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta" id="about">
        <div class="cta-content">
            <h2>Ready to Transform Your Institution?</h2>
            <p>Join thousands of educators who trust Simple Tuition for their management needs. Get started in minutes, no credit card required.</p>
            <div style="display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap;">
                <a href="student/register.php" class="btn-primary" style="font-size: 1.1rem; padding: 1rem 2.5rem;">Get Started Free</a>
                <a href="admin/login.php" class="btn-outline" style="font-size: 1.1rem; padding: 1rem 2.5rem; color: white; border-color: white;">Admin Login</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="logo" style="color: white; margin-bottom: 1rem;">
                    <div class="logo-icon">📚</div>
                    <span>Simple Tuition</span>
                </div>
                <p>Modern education management made simple. Streamline your institution with powerful tools and intuitive design.</p>
            </div>
            <div class="footer-links">
                <h4>Product</h4>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#portals">Portals</a></li>
                    <li><a href="student/register.php">Register</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Access</h4>
                <ul>
                    <li><a href="admin/login.php">Admin Login</a></li>
                    <li><a href="student/login.php">Student Login</a></li>
                    <li><a href="student/register.php">Student Register</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Support</h4>
                <ul>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#features">Documentation</a></li>
                    <li><a href="#portals">Help Center</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Simple Tuition Management System. All rights reserved. </p>
        </div>
    </footer>

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
    </script>
</body>
</html>