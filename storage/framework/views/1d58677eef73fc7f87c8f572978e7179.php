<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lalon Airport - Your Gateway to the World</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #1e40af;
            --light-blue: #3b82f6;
            --sky-blue: #60a5fa;
            --dark-bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.9);
            --text-light: #e2e8f0;
            --text-muted: #94a3b8;
            --accent-gold: #fbbf24;
            --accent-teal: #2dd4bf;
            --gradient-primary: linear-gradient(135deg, #1e40af, #3b82f6);
            --gradient-secondary: linear-gradient(135deg, #3b82f6, #60a5fa);
            --gradient-accent: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            color: var(--text-light);
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .cloud {
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
            box-shadow: 0 0 60px rgba(255, 255, 255, 0.1);
        }

        .cloud:nth-child(1) { width: 300px; height: 300px; top: 10%; left: -150px; animation-delay: 0s; }
        .cloud:nth-child(2) { width: 200px; height: 200px; top: 40%; right: -100px; animation-delay: 3s; }
        .cloud:nth-child(3) { width: 250px; height: 250px; bottom: 20%; left: 50%; animation-delay: 6s; }
        .cloud:nth-child(4) { width: 180px; height: 180px; top: 60%; left: 10%; animation-delay: 9s; }
        .cloud:nth-child(5) { width: 220px; height: 220px; top: 20%; right: 20%; animation-delay: 12s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-30px) translateX(30px); }
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(15px);
            padding: 1.2rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(59, 130, 246, 0.2);
        }

        nav.scrolled {
            padding: 0.8rem 5%;
            box-shadow: 0 6px 30px rgba(59, 130, 246, 0.3);
            background: rgba(15, 23, 42, 0.98);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--sky-blue);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logo:hover {
            transform: translateY(-2px);
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
            transition: all 0.3s ease;
        }

        .logo:hover .logo-icon {
            transform: rotate(10deg);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.7);
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2.5rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            position: relative;
            transition: color 0.3s ease;
            padding: 0.5rem 0;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--sky-blue);
            transition: width 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--sky-blue);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .login-btn {
            background: var(--gradient-primary);
            padding: 0.7rem 1.8rem;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.6);
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            z-index: 1001;
        }

        .menu-toggle span {
            width: 25px;
            height: 3px;
            background: var(--sky-blue);
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Hero Section */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8rem 5% 4rem;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 900px;
            animation: fadeInUp 1s ease;
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

        .hero h1 {
            font-size: 4.5rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--sky-blue), var(--accent-teal));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 5px 15px rgba(96, 165, 250, 0.3);
        }

        .hero p {
            font-size: 1.4rem;
            color: var(--text-muted);
            margin-bottom: 3rem;
            line-height: 1.6;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Search Box */
        .search-box {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(59, 130, 246, 0.3);
            margin-top: 2rem;
            animation: fadeInUp 1.2s ease;
            position: relative;
            overflow: hidden;
        }

        .search-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gradient-primary);
        }

        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            position: relative;
        }

        .form-group label {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            padding: 1.2rem;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(59, 130, 246, 0.4);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--sky-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            background: rgba(15, 23, 42, 0.9);
        }

        .search-btn {
            padding: 1.2rem 3rem;
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 0 auto;
        }

        .search-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .search-btn:hover::before {
            left: 100%;
        }

        .search-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(59, 130, 246, 0.6);
        }

        /* Quick Actions */
        .quick-actions {
            padding: 6rem 5%;
            position: relative;
            z-index: 10;
        }

        .section-title {
            text-align: center;
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 4rem;
            color: var(--sky-blue);
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-accent);
            border-radius: 2px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .action-card {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            padding: 2.5rem;
            border-radius: 20px;
            text-align: center;
            transition: all 0.4s ease;
            border: 1px solid rgba(59, 130, 246, 0.3);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .action-card:hover::before {
            left: 100%;
        }

        .action-card:hover {
            transform: translateY(-15px) scale(1.03);
            box-shadow: 0 20px 50px rgba(59, 130, 246, 0.4);
            border-color: var(--sky-blue);
        }

        .action-icon {
            width: 90px;
            height: 90px;
            background: var(--gradient-primary);
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.8rem;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
            transition: all 0.3s ease;
        }

        .action-card:hover .action-icon {
            transform: rotate(10deg) scale(1.1);
            background: var(--gradient-accent);
        }

        .action-card h3 {
            font-size: 1.6rem;
            margin-bottom: 1rem;
            color: var(--sky-blue);
            transition: color 0.3s ease;
        }

        .action-card:hover h3 {
            color: var(--accent-gold);
        }

        .action-card p {
            color: var(--text-muted);
            line-height: 1.6;
            font-size: 1rem;
        }

        /* Features Section */
        .features {
            padding: 6rem 5%;
            position: relative;
            z-index: 10;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            padding: 2rem;
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border-radius: 18px;
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .feature-item:hover {
            border-color: var(--sky-blue);
            transform: translateY(-10px) translateX(5px);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.3);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-primary);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .feature-item:hover .feature-icon {
            transform: rotate(15deg) scale(1.1);
            background: var(--gradient-accent);
        }

        .feature-item h4 {
            font-size: 1.3rem;
            margin-bottom: 0.7rem;
            color: var(--sky-blue);
            transition: color 0.3s ease;
        }

        .feature-item:hover h4 {
            color: var(--accent-gold);
        }

        .feature-item p {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Stats Section */
        .stats {
            padding: 6rem 5%;
            position: relative;
            z-index: 10;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2.5rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .stat-card {
            text-align: center;
            padding: 2.5rem;
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border-radius: 18px;
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            border-color: var(--sky-blue);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.3);
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 900;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.7rem;
            line-height: 1;
        }

        .stat-card:hover .stat-number {
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Footer */
        footer {
            background: rgba(15, 23, 42, 0.98);
            padding: 4rem 5%;
            position: relative;
            z-index: 10;
            margin-top: 4rem;
            border-top: 1px solid rgba(59, 130, 246, 0.3);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3.5rem;
            max-width: 1200px;
            margin: 0 auto 3rem;
        }

        .footer-section h3 {
            color: var(--sky-blue);
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
            position: relative;
            display: inline-block;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--gradient-accent);
            border-radius: 2px;
        }

        .footer-section p,
        .footer-section a {
            color: var(--text-muted);
            text-decoration: none;
            display: block;
            margin-bottom: 0.8rem;
            transition: color 0.3s ease;
            line-height: 1.6;
        }

        .footer-section a:hover {
            color: var(--sky-blue);
            transform: translateX(5px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2.5rem;
            border-top: 1px solid rgba(59, 130, 246, 0.3);
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* New: Newsletter Section */
        .newsletter {
            padding: 5rem 5%;
            position: relative;
            z-index: 10;
            background: rgba(15, 23, 42, 0.7);
            margin: 4rem 0;
            border-radius: 20px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .newsletter-content {
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }

        .newsletter h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--sky-blue);
        }

        .newsletter p {
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
            gap: 1rem;
        }

        .newsletter-form input {
            flex: 1;
            padding: 1.2rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(59, 130, 246, 0.4);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 1rem;
        }

        .newsletter-form button {
            padding: 1.2rem 2rem;
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .newsletter-form button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            .nav-links {
                position: fixed;
                top: 0;
                left: -100%;
                flex-direction: column;
                background: rgba(15, 23, 42, 0.98);
                width: 100%;
                height: 100vh;
                padding: 6rem 2rem 2rem;
                gap: 1.5rem;
                transition: left 0.3s ease;
                backdrop-filter: blur(15px);
            }

            .nav-links.active {
                left: 0;
            }

            .hero h1 {
                font-size: 3rem;
            }

            .hero p {
                font-size: 1.2rem;
            }

            .search-form {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 2.2rem;
            }

            .newsletter-form {
                flex-direction: column;
            }

            .action-card, .feature-item, .stat-card {
                padding: 2rem;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .search-box, .action-card, .feature-item, .stat-card {
                padding: 1.5rem;
            }
        }

        /* Scroll to top button */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            z-index: 100;
            box-shadow: 0 5px 20px rgba(59, 130, 246, 0.5);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }

        .scroll-top.active {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.7);
        }

        /* Success notification */
        .success-notification {
            position: fixed;
            top: 100px;
            right: 30px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            z-index: 1001;
            animation: slideInRight 0.5s ease, fadeOut 0.5s ease 4.5s forwards;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(400px);
            }
        }
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="cloud"></div>
        <div class="cloud"></div>
        <div class="cloud"></div>
        <div class="cloud"></div>
        <div class="cloud"></div>
    </div>

    <?php if(session('success')): ?>
        <div class="success-notification">
            <i class="fas fa-check-circle"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <nav id="navbar">
        <a href="#" class="logo">
            <div class="logo-icon"><i class="fas fa-plane"></i></div>
            <span>Lalon Airport</span>
        </a>
        <ul class="nav-links" id="navLinks">
            <li><a href="#home">Home</a></li>
            <li><a href="<?php echo e(Route::has('flights.index') ? route('flights.index') : url('/flights')); ?>">Flights</a></li>
            <li><a href="<?php echo e(Route::has('flights.index') ? route('flights.index') : url('/flights')); ?>">Booking</a></li>
            <li><a href="<?php echo e(route('status')); ?>">Status</a></li>
            <li><a href="<?php echo e(route('contact')); ?>">Contact</a></li>
            <?php if(auth()->guard()->check()): ?>
                <li style="color: var(--sky-blue); font-weight: 600;">
                    <i class="fas fa-user-circle"></i> <?php echo e(Auth::user()->name); ?>

                </li>
                <li>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="login-btn" style="background: linear-gradient(135deg, #dc2626, #b91c1c); border: none; cursor: pointer;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="<?php echo e(route('login.dashboard')); ?>" class="login-btn">Login</a></li>
            <?php endif; ?>
        </ul>
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Your Gateway to the World</h1>
            <p>Experience seamless travel with Lalon Airport. Book flights, check-in online, and track your journey in real-time with our state-of-the-art facilities.</p>
            
            <div class="search-box">
                <form class="search-form" method="GET" action="<?php echo e(route('flights.index')); ?>">
                    <div class="form-group">
                        <label><i class="fas fa-plane-departure"></i> From</label>
                        <input type="text" name="origin" placeholder="Departure City" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-plane-arrival"></i> To</label>
                        <input type="text" name="destination" placeholder="Destination City" required>
                    </div>
                    <div class="form-group">
                        <label><i class="far fa-calendar-alt"></i> Departure Date</label>
                        <input type="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user-friends"></i> Class</label>
                        <select name="class">
                            <option value="economy">Economy</option>
                            <option value="business">Business</option>
                            <option value="first">First Class</option>
                        </select>
                    </div>
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i> Search Flights</button>
                </form>
            </div>
        </div>
    </section>

    <section class="quick-actions">
        <h2 class="section-title">Quick Actions</h2>
        <div class="actions-grid">
            <a class="action-card" href="<?php echo e(route('flights.index')); ?>" style="text-decoration:none;color:inherit">
                <div class="action-icon"><i class="fas fa-ticket-alt"></i></div>
                <h3>Book Flight</h3>
                <p>Find and book your perfect flight with our easy booking system and exclusive deals</p>
            </a>
            <a class="action-card" href="<?php echo e(route('checkin.create')); ?>" style="text-decoration:none;color:inherit" id="quickCheckInCard" data-terminals="5">
                <div class="action-icon"><i class="fas fa-check-circle"></i></div>
                <h3>Check-In</h3>
                <p>Complete online check-in and get your digital boarding pass instantly</p>

            </a>
            <a href="<?php echo e(route('flight_status')); ?>" class="action-card" style="text-decoration:none;color:inherit">

                <div class="action-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Flight Status</h3>
                <p>Track real-time flight information, gate updates and arrival times</p>
            </a>
            <a href="<?php echo e(route('baggage_track')); ?>" class="action-card" style="text-decoration:none;color:inherit">
                <div class="action-icon"><i class="fas fa-suitcase-rolling"></i></div>
                <h3>Baggage Track</h3>
                <p>Monitor your baggage location throughout your journey with our tracking system</p>
            </a>
        </div>
    </section>

    <section class="stats">
        <h2 class="section-title">Lalon Airport by Numbers</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">500+</div>
                <div class="stat-label">Daily Flights</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">150</div>
                <div class="stat-label">Destinations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">50K+</div>
                <div class="stat-label">Daily Passengers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support</div>
            </div>
        </div>
    </section>

    <section class="features">
        <h2 class="section-title">Why Choose Lalon Airport?</h2>
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <div>
                    <h4>Fast Check-In</h4>
                    <p>Complete check-in in under 2 minutes with our streamlined process</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <div>
                    <h4>Secure Payments</h4>
                    <p>Bank-grade encryption for all transactions ensuring your data safety</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-globe-americas"></i></div>
                <div>
                    <h4>Global Network</h4>
                    <p>Connected to 150+ destinations worldwide with premium airlines</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
                <div>
                    <h4>Mobile Ready</h4>
                    <p>Manage bookings on any device with our responsive platform</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-credit-card"></i></div>
                <div>
                    <h4>Flexible Payment</h4>
                    <p>Multiple payment options including installments and digital wallets</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <div>
                    <h4>24/7 Support</h4>
                    <p>Always here to help you with our round-the-clock customer service</p>
                </div>
            </div>
        </div>
    </section>
  

    <section class="newsletter">
        <div class="newsletter-content">
            <h2>Stay Updated</h2>
            <p>Subscribe to our newsletter for exclusive deals, travel tips, and airport updates</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Lalon Airport</h3>
                <p>Your trusted travel partner connecting you to the world with excellence and care. Experience the future of air travel with us.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="<?php echo e(route('about')); ?>">About Us</a>
                <a href="<?php echo e(route('careers')); ?>">Careers</a>
                <a href="<?php echo e(route('news')); ?>">News & Media</a>
                <a href="<?php echo e(route('investor')); ?>">Investor Relations</a>
            </div>
            <div class="footer-section">
                <h3>Services</h3>
                <a href="<?php echo e(route('services.flight_booking')); ?>">Flight Booking</a>
                <a href="<?php echo e(route('services.online_checkin')); ?>">Online Check-in</a>
                <a href="<?php echo e(route('services.baggage_services')); ?>">Baggage Services</a>
                <a href="<?php echo e(route('services.lounges')); ?>">Lounges</a>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p><i class="fas fa-envelope"></i> Email: info@lalonairport.com</p>
                <p><i class="fas fa-phone"></i> Phone: +880-123-456789</p>
                <p><i class="fas fa-headset"></i> Support: 24/7 Available</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Lalon Airport. All rights reserved. | Designed with ❤ for seamless travel</p>
        </div>
    </footer>

    <div class="scroll-top" id="scrollTop">
        <i class="fas fa-chevron-up"></i>
    </div>

    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });

        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    navLinks.classList.remove('active');
                    menuToggle.classList.remove('active');
                }
            });
        });

        // Action cards click animation
        document.querySelectorAll('.action-card').forEach(card => {
            card.addEventListener('click', function(e) {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => { this.style.transform = ''; }, 200);
            });
        });

        // Assign a rotating terminal (1..5) when using the quick Check-In card
        const quickCheckInCard = document.getElementById('quickCheckInCard');
        if (quickCheckInCard) {
            quickCheckInCard.addEventListener('click', function(e) {
                // Compute terminal and rewrite href just before navigation
                let last = Number(localStorage.getItem('lalon_last_terminal')) || 0;
                const max = Number(quickCheckInCard.getAttribute('data-terminals')) || 5;
                const next = (last % max) + 1;
                localStorage.setItem('lalon_last_terminal', String(next));

                try {
                    const url = new URL(quickCheckInCard.href, window.location.origin);
                    url.searchParams.set('terminal', String(next));
                    quickCheckInCard.setAttribute('href', url.toString());
                } catch (_) { /* noop */ }
            }, { once: true });
        }

        // Number counter animation
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200;

        counters.forEach(counter => {
            const updateCount = () => {
                const target = counter.innerText;
                const count = 0;
                const numericTarget = parseInt(target.replace(/\D/g, ''));
                
                if (isNaN(numericTarget)) return;

                const inc = numericTarget / speed;

                const timer = setInterval(() => {
                    const current = parseInt(counter.innerText.replace(/\D/g, '')) || 0;
                    if (current < numericTarget) {
                        const newValue = Math.ceil(current + inc);
                        const suffix = target.includes('K') ? 'K+' : target.includes('/') ? '/7' : '+';
                        counter.innerText = newValue + suffix;
                    } else {
                        counter.innerText = target;
                        clearInterval(timer);
                    }
                }, 1);
            };

            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    updateCount();
                    observer.disconnect();
                }
            });

            observer.observe(counter);
        });

        // Scroll to top button
        const scrollTopBtn = document.getElementById('scrollTop');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('active');
            } else {
                scrollTopBtn.classList.remove('active');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Search form: show spinner but allow normal navigation to /flights
        const searchForm = document.querySelector('.search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                const btn = this.querySelector('.search-btn');
                if (btn) btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
            });
        }

        document.querySelector('.newsletter-form button').addEventListener('click', function(e) {
            e.preventDefault();
            const email = document.querySelector('.newsletter-form input').value;
            if (email) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
                setTimeout(() => {
                    this.innerHTML = 'Subscribed!';
                    document.querySelector('.newsletter-form input').value = '';
                    setTimeout(() => {
                        this.innerHTML = 'Subscribe';
                    }, 2000);
                }, 1500);
            }
        });
    </script>
</body>
</html><?php /**PATH C:\Users\HP\example-app\resources\views/welcome.blade.php ENDPATH**/ ?>