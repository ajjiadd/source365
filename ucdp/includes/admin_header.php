<?php
// Admin-specific header - Separate from user header
require_once __DIR__ . '/../config.php';  // Fixed path
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Unified Citizen Document Portal (UCDP)</title>
    <meta name="description" content="Admin dashboard for document verification and monitoring." />
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">  <!-- Shared CSS -->
    <script src="<?php echo SITE_URL; ?>assets/js/script.js" defer></script>
</head>
<body>
<!-- ADMIN HEADER - Green theme, admin nav -->
<header class="site-header" style="background: linear-gradient(90deg, var(--gov-green), var(--gov-deep));">
    <div class="container site-top">
        <div class="brand">
            <div class="seal" aria-hidden="true">
                <img src="<?php echo SITE_URL; ?>assets/images/bd_logo.png" alt="Gov BD Logo" srcset="">
            </div>
            <div>
                <h1 style="color: #fff; font-size: 18px; margin: 0;">UCDP Admin Panel</h1>
                <p class="small" style="margin: 0; opacity: 0.9;">Admin Dashboard — Government of Bangladesh</p>
            </div>
        </div>

        <nav class="main-nav" aria-label="Admin navigation" style="gap: 20px;">
            <a href="<?php echo SITE_URL; ?>admin/dashboard.php" style="color: rgba(255, 255, 255, 0.92);">Dashboard</a>
            <a href="<?php echo SITE_URL; ?>admin/verify_documents.php" style="color: rgba(255, 255, 255, 0.92);">Verify Documents</a>
            <a href="<?php echo SITE_URL; ?>admin/monitor_logs.php" style="color: rgba(255, 255, 255, 0.92);">Monitor Logs</a>
            <a href="<?php echo SITE_URL; ?>admin/logout.php" style="color: #fff; background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 6px;">Logout</a>
        </nav>

        <div class="auth-btns" style="display: none;"> <!-- Hidden for admin -->
            <!-- No user login/register here -->
        </div>
    </div>
</header>

<main class="container" style="padding-top: 20px;">