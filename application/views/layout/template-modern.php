<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAT - Sistem Inventori ATK Terpadu | <?= $page ?? 'Dashboard' ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/siat-modern.css">
    <link rel="icon" href="<?= base_url() ?>/assets/dist/img/favicon-16x16.png" type="image/gif">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Select2 Theme Override - Match form input style -->
    <style>
        /* Select2 container */
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            padding: 0 12px;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            display: flex;
            align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            color: #1e293b;
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px !important;
            padding-left: 0 !important;
            padding-right: 24px !important;
            color: #1e293b;
            font-size: 14px;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            right: 10px !important;
            top: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #64748b transparent transparent transparent;
        }
        /* Focus state */
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
            outline: none;
        }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #3b82f6 transparent !important;
        }
        /* Dropdown container */
        .select2-container--default .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1) !important;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Search box */
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            margin: 6px !important;
            width: calc(100% - 12px) !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #3b82f6 !important;
            outline: none;
            box-shadow: 0 0 0 2px rgba(59,130,246,0.15);
        }
        /* Options */
        .select2-container--default .select2-results__option {
            padding: 9px 14px !important;
            font-size: 14px !important;
            color: #374151;
            cursor: pointer;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
        }
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #dbeafe !important;
            color: #1d4ed8 !important;
            font-weight: 500;
        }
        .select2-results__message {
            color: #94a3b8;
            font-size: 13px;
            padding: 10px 14px;
        }
        /* Full width */
        .select2-container {
            width: 100% !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <style>
        :root {
            --primary: #1e293b;
            --primary-light: #334155;
            --primary-dark: #0f172a;
            --accent: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --surface: #ffffff;
            --surface-elevated: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --radius-sm: 6px;
            --radius: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        /* Layout */
        .app-layout {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
        }
        
        .sidebar-logo img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }
        
        .sidebar-logo-text {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.3;
        }
        
        .sidebar-logo-sub {
            font-size: 11px;
            opacity: 0.7;
            font-weight: 400;
        }
        
        .sidebar-nav {
            padding: 16px 12px;
        }
        
        .nav-section {
            margin-bottom: 24px;
        }
        
        .nav-section-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.5);
            padding: 0 12px;
            margin-bottom: 8px;
        }
        
        .nav-item {
            margin-bottom: 4px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: var(--radius);
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .nav-link.active {
            background: var(--accent);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 10px;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .page-content {
            flex: 1;
        }
        
        /* Header */
        .header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-primary);
            cursor: pointer;
            padding: 8px;
            border-radius: var(--radius);
            transition: background 0.2s;
        }
        
        .menu-toggle:hover {
            background: var(--surface-elevated);
        }
        
        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        
        .header-btn {
            width: 40px;
            height: 40px;
            border-radius: var(--radius);
            border: none;
            background: var(--surface-elevated);
            color: var(--text-secondary);
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .header-btn:hover {
            background: var(--border);
            color: var(--text-primary);
        }
        
        .header-btn .badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 18px;
            height: 18px;
            background: var(--danger);
            color: white;
            font-size: 10px;
            font-weight: 600;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        /* User Dropdown */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: var(--surface-elevated);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        
        .user-menu:hover {
            background: var(--border);
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        
        .user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 240px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            z-index: 1000;
        }
        
        .user-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid var(--border);
        }
        
        .dropdown-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }
        
        .dropdown-info {
            display: flex;
            flex-direction: column;
        }
        
        .dropdown-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-primary);
        }
        
        .dropdown-email {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 14px;
            transition: background 0.15s;
        }
        
        .dropdown-item:hover {
            background: var(--surface-elevated);
        }
        
        .dropdown-item i {
            width: 20px;
            color: var(--text-secondary);
        }
        
        .dropdown-item.logout {
            color: var(--danger);
        }
        
        .dropdown-item.logout i {
            color: var(--danger);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-info {
            text-align: left;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .user-role {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        /* Page Content */
        .page-content {
            padding: 24px 32px;
        }
        
        /* Cards */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
        }
        
        .card:hover {
            box-shadow: var(--shadow);
        }
        
        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .card-body {
            padding: 24px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        
        .stat-icon.primary {
            background: rgba(59, 130, 246, 0.1);
            color: var(--accent);
        }
        
        .stat-icon.success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .stat-icon.warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        
        .stat-icon.danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 4px;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }
        
        .stat-change {
            font-size: 12px;
            margin-top: 4px;
        }
        
        .stat-change.positive {
            color: var(--success);
        }
        
        .stat-change.negative {
            color: var(--danger);
        }
        
        /* Tables */
        .table-container {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        
        .table th {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            background: var(--surface-elevated);
        }
        
        .table tbody tr {
            transition: background 0.15s;
        }
        
        .table tbody tr:hover {
            background: var(--surface-elevated);
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border-radius: var(--radius);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-secondary {
            background: var(--surface-elevated);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: var(--border);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background: #059669;
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .btn-lg {
            padding: 14px 28px;
            font-size: 16px;
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-size: 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
            color: var(--text-primary);
            transition: all 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-control::placeholder {
            color: var(--text-secondary);
        }
        
        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 20px;
        }
        
        .badge-primary {
            background: rgba(59, 130, 246, 0.1);
            color: var(--accent);
        }
        
        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        
        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        /* Alerts */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #065f46;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #991b1b;
        }
        
        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            color: #92400e;
        }
        
        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #1e40af;
        }
        
        /* Footer */
        .footer {
            padding: 20px 32px;
            border-top: 1px solid var(--border);
            background: var(--surface);
            margin-top: auto;
        }
        
        .footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .footer-text {
            font-size: 13px;
            color: var(--text-secondary);
        }
        
        /* Loading Overlay */
        #loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 9999;
            backdrop-filter: blur(4px);
        }
        
        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 48px;
            height: 48px;
            border: 3px solid var(--border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: flex;
            }
            
            .page-content {
                padding: 16px;
            }
            
            .header {
                padding: 12px 16px;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease forwards;
        }
        
        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
    </style>
</head>

<body>
    <div id="loading-overlay">
        <div class="loading-spinner"></div>
    </div>
    
    <?php
    $unread_notifications = 0;
    $active_requests_count = 0;
    
    $ci =& get_instance();
    $user_id = (int) $ci->session->userdata('id_user');
    $user_level = (int) $ci->session->userdata('level');
    
    if ($user_id > 0) {
        $ci->load->model('Notification_model');
        $unread_notifications = $ci->Notification_model->count_unread($user_id);
        
        if ($user_level === 1) {
            $ci->load->model('Request_model');
            $active_requests_count = $ci->Request_model->count_active_requests();
        }
    }
    ?>
    
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="app-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="<?= base_url('dashboard') ?>" class="sidebar-logo">
                    <img src="<?= base_url() ?>assets/dist/img/logopakota.png" alt="SIAT Logo">
                    <div>
                        <div class="sidebar-logo-text">SIAT</div>
                        <div class="sidebar-logo-sub">Sistem Inventori ATK</div>
                    </div>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <?php include "nav-modern.php"; ?>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title"><?= $page ?? 'Dashboard' ?></h1>
                </div>
                
                <div class="header-right">
                    <div class="header-btn" style="background: var(--surface-elevated); color: var(--text-primary); border-radius: 20px; padding: 0 16px; font-size: 13px; font-weight: 600; cursor: default; width: auto; border: 1px solid var(--border);" title="Tahun Login Aktif">
                        <i class="fas fa-calendar-alt" style="margin-right: 6px; color: var(--accent);"></i>
                        <?= $this->session->userdata('login_year') ?? date('Y') ?>
                    </div>

                    <button class="header-btn" id="fullscreenBtn" title="Fullscreen">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </button>
                    
                    <a href="<?= site_url('notification') ?>" class="header-btn" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <?php if (!empty($unread_notifications) && (int) $unread_notifications > 0): ?>
                            <span class="badge"><?= (int) $unread_notifications ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <div class="user-menu" id="userMenu">
                        <div class="user-avatar">
                            <?php
                            $user_login = $this->fungsi->user_login();
                            echo $user_login ? strtoupper(substr($user_login->username, 0, 2)) : 'US';
                            ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?= $user_login->username ?? 'User' ?></div>
                            <div class="user-role">
                                <?php
                                $level = (int) $this->session->userdata('level');
                                echo $level === 1 ? 'Administrator' : 'Pegawai';
                                ?>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down" style="color: var(--text-secondary); font-size: 12px;"></i>
                    </div>
                    
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <div class="dropdown-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="dropdown-info">
                                <span class="dropdown-name"><?= $user_login->nama ?? $user_login->username ?? 'User' ?></span>
                                <span class="dropdown-email"><?= $user_login->username ?? '' ?></span>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="<?= site_url('auth/change_password') ?>" class="dropdown-item">
                            <i class="fas fa-key"></i>
                            <span>Ubah Password</span>
                        </a>
                        <a href="<?= site_url('guide') ?>" class="dropdown-item">
                            <i class="fas fa-book"></i>
                            <span>Panduan</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?= site_url('auth/logout') ?>" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="page-content animate-fade-in">
                <?= $contents ?>
            </div>
            
            <!-- Footer -->
            <footer class="footer">
                <div class="footer-content">
                    <div class="footer-text">
                        <strong>SIAT - Sistem Inventori ATK Terpadu</strong> &copy; <?= date('Y') ?>. All rights reserved.
                    </div>
                    <div class="footer-text">
                        Developed by Rahmat Triadi, S.Kom.
                    </div>
                </div>
            </footer>
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000"
        };

        <?php if ($ci->session->flashdata('success')): ?>
            toastr.success('<?= str_replace("'", "\\'", strip_tags($ci->session->flashdata('success'))) ?>');
        <?php endif; ?>

        <?php if ($ci->session->flashdata('error') || $ci->session->flashdata('danger') || isset($error)): ?>
            toastr.error('<?= str_replace("'", "\\'", strip_tags($ci->session->flashdata('error') ?? $ci->session->flashdata('danger') ?? ($error ?? ''))) ?>');
        <?php endif; ?>

        <?php if ($ci->session->flashdata('warning')): ?>
            toastr.warning('<?= str_replace("'", "\\'", strip_tags($ci->session->flashdata('warning'))) ?>');
        <?php endif; ?>

        <?php if ($ci->session->flashdata('info')): ?>
            toastr.info('<?= str_replace("'", "\\'", strip_tags($ci->session->flashdata('info'))) ?>');
        <?php endif; ?>

        // Global Select2 initialization
        $(function() {
            $('select:not(.no-select2)').select2({
                width: '100%',
                placeholder: function() { return $(this).data('placeholder') || '-- Pilih --'; },
                allowClear: false,
                language: {
                    noResults: function() { return 'Tidak ada hasil'; },
                    searching: function() { return 'Mencari...'; }
                }
            });
        });
        
        // Sidebar Toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('active');
        });
        
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
        });
        
        // Fullscreen
        document.getElementById('fullscreenBtn').addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        });
        
        // AJAX Loading
        $(document).ajaxStart(function () {
            $('#loading-overlay').show();
        });
        
        $(document).ajaxStop(function () {
            $('#loading-overlay').hide();
        });
        
        // Active nav link
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.classList.contains('active')) {
                link.style.background = 'var(--accent)';
            }
        });
        
        // User Dropdown Toggle
        const userMenu = document.getElementById('userMenu');
        const userDropdown = document.getElementById('userDropdown');
        
        userMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
        
        userDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
        
        document.addEventListener('click', (e) => {
            if (!userDropdown.contains(e.target) && !userMenu.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    </script>
</body>

</html>
