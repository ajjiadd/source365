<?php
require_once DIR . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unified Citizen Document Portal-একীভূত নাগরিক ডকুমেন্ট পোর্টাল</title>
    <meta name="description" content="আপনার সকল সরকারি ডকুমেন্ট এক জায়গায় — নিরাপদ, সহজ, নির্ভরযোগ্য। NID, Passport, Driving Licence ইত্যাদি।" />
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">  
    <script src="<?php echo SITE_URL; ?>assets/js/script.js" defer></script> 
</head>
<body>
<!-- HEADER -->
<header class="site-header">
    <div class="container site-top">
        <div class="brand">
            <div class="seal" aria-hidden="true">
                <img src="<?php echo SITE_URL; ?>assets/images/bd_logo.png" alt="" srcset="">  <!-- Absolute image -->
            </div>
            <div>
                <h1>Unified Citizen Document Portal - UCDP</h1>
                <p class="small">একীভূত নাগরিক ডকুমেন্ট পোর্টাল — Government of Bangladesh</p>
            </div>
        </div>

        <nav class="main-nav" aria-label="Main navigation">
            <a href="<?php echo SITE_URL; ?>index.php#home">Home</a>
            <a href="<?php echo SITE_URL; ?>index.php#about">About</a>
            <a href="<?php echo SITE_URL; ?>index.php#how">How It Works</a>
            <a href="<?php echo SITE_URL; ?>index.php#services">Services</a>
            <a href="<?php echo SITE_URL; ?>index.php#contact">Contact</a>
            <span class="lang-btn" role="button" tabindex="0" onclick="toggleLang()">বাংলা</span>
        </nav>

        <div class="auth-btns">
            <a class="btn-login" href="<?php echo SITE_URL; ?>user/login.php">Login</a>
            <a class="btn-register" href="<?php echo SITE_URL; ?>user/register.php">Register</a>
        </div>
    </div>
</header>

<main class="container">
<script>
function toggleLang() {
    alert('Language switch coming soon!');  
}
</script>