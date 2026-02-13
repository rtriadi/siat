<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAT - Sistem Inventori ATK Terpadu | Login</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #1e293b;
            --primary-light: #334155;
            --accent: #3b82f6;
            --accent-hover: #2563eb;
            --success: #10b981;
            --surface: #ffffff;
            --surface-elevated: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --radius: 12px;
            --radius-lg: 16px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background */
        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .bg-pattern::before,
        .bg-pattern::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            opacity: 0.1;
        }
        
        .bg-pattern::before {
            background: var(--accent);
            top: -200px;
            right: -200px;
            animation: float 20s ease-in-out infinite;
        }
        
        .bg-pattern::after {
            background: var(--primary);
            bottom: -200px;
            left: -200px;
            animation: float 15s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, 30px); }
        }
        
        /* Floating shapes */
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.05;
            z-index: 0;
        }
        
        .shape-1 {
            width: 300px;
            height: 300px;
            background: var(--accent);
            top: 10%;
            left: 5%;
            animation: float 25s ease-in-out infinite;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            background: var(--primary);
            bottom: 15%;
            right: 10%;
            animation: float 20s ease-in-out infinite 2s;
        }
        
        .shape-3 {
            width: 150px;
            height: 150px;
            background: var(--success);
            top: 60%;
            left: 20%;
            animation: float 18s ease-in-out infinite 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(10deg); }
        }
        
        /* Login Container */
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }
        
        .login-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 48px 40px;
            animation: slideUp 0.6s ease forwards;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Logo Section */
        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(30, 41, 59, 0.3);
            animation: pulse 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .login-logo img {
            width: 50px;
            height: 50px;
            filter: brightness(0) invert(1);
        }
        
        .login-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        
        .login-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 16px;
            transition: color 0.2s;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            font-size: 15px;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface-elevated);
            color: var(--text-primary);
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            background: var(--surface);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        
        .form-control:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--accent);
        }
        
        .form-control::placeholder {
            color: var(--text-secondary);
        }
        
        /* Button */
        .btn-login {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: var(--radius);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(30, 41, 59, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(30, 41, 59, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login i {
            transition: transform 0.3s ease;
        }
        
        .btn-login:hover i {
            transform: translateX(4px);
        }
        
        /* Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #991b1b;
        }
        
        .alert-danger i {
            font-size: 18px;
        }
        
        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }
        
        .login-footer-text {
            font-size: 13px;
            color: var(--text-secondary);
        }
        
        .login-footer-text a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer-text a:hover {
            text-decoration: underline;
        }
        
        /* Decorative Elements */
        .card-decoration {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--success), var(--accent));
            background-size: 200% 100%;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            animation: gradientShift 3s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Footer watermark */
        .footer-watermark {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: var(--text-secondary);
            opacity: 0.6;
        }
        
        .footer-watermark a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="bg-pattern"></div>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="card-decoration"></div>
            
            <div class="login-header">
                <div class="login-logo">
                    <img src="<?= base_url() ?>assets/dist/img/logo_pta.png" alt="SIAT Logo">
                </div>
                <h1 class="login-title">SIAT</h1>
                <p class="login-subtitle">Sistem Inventori ATK Terpadu</p>
            </div>
            
            <?php
            $ci = &get_instance();
            if ($ci->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Gagal!</strong> <?= $ci->session->flashdata('error') ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <form action="<?= site_url('auth/login') ?>" method="post">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-wrapper">
                        <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username Anda" required autocomplete="off">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password Anda" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <span>Masuk</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            
            <div class="login-footer">
                <p class="login-footer-text">
                    &copy; <?= date('Y') ?>. Developed by <strong>Rahmat Triadi, S.Kom.</strong>
                </p>
            </div>
        </div>
    </div>
    
    <div class="footer-watermark">
        SIAT v2.0 | Sistem Inventori ATK Terpadu
    </div>
    
    <script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
