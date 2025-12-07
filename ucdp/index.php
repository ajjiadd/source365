<?php
// Main landing page
require_once 'includes/header.php';  
?>

<!-- hero section  -->
<section id="home" class="hero" aria-labelledby="hero-heading">
    <div class="hero-left">
        <h2 id="hero-heading">আপনার সকল সরকারি কাগজপত্র এখন এক জায়গায়</h2>
        <p>NID, Passport, Birth Certificate, Driving Licence ও অন্যান্য গুরুত্বপূর্ণ ডকুমেন্ট এক সিঙ্গেল
            পোর্টালে — নিরাপদভাবে সংরক্ষণ ও সহজে এক্সেস করুন।</p>

        <div class="hero-ctas">
            <button class="cta-primary" onclick="location.href='user/register.php'">Register / রেজিস্টার</button>
            <a class="cta-secondary" href="#how">How it works</a>
        </div>

        <div class="hero-stats">
            <div class="stat-item"><strong style="color:var(--gov-green); margin-right:6px;">✔</strong> Single
                Sign-On</div>
            <div class="stat-item"><strong style="color:var(--gov-green); margin-right:6px;">✔</strong> Official
                integrations</div>
            <div class="stat-item"><strong style="color:var(--gov-green); margin-right:6px;">✔</strong>
                Encrypted storage & 2FA</div>
            <div class="stat-item"><strong style="color:var(--gov-green); margin-right:6px;">✔</strong> Family
                linking & verified downloads</div>
        </div>
    </div>

    <aside class="hero-mock" aria-hidden="true">
        <h3 style="margin:0 0 8px 0; font-size:16px;">নাগরিক তথ্যের উদাহরণ</h3>

        <!--imgs slider -->
        <div class="slider-container">
            <div class="slider">
                <img src="assets/images/NID-Card_baner.jpg" alt="Government Document Banner 1">
                <img src="assets/images/driving-licence_baner.png" alt="Government Document Banner 2">
                <img src="assets/images/Passport_pic.jpg" alt="Government Document Banner 3">
            </div>
        </div>

        <!-- demo interface -->
        <div class="mock-row">Name: <strong>MD/MST. STYLAXX</strong></div>
        <div class="mock-row">
            NID: <strong>450336-4869xxx</strong>
        </div>
        <div class="mock-row">Passport: <strong>Active</strong></div>
        <div class="mock-row">Driving Licence: <strong>Valid till 2028</strong></div>
        <p class="small" style="margin-top:8px;">Government documents in a single portal — securely stored and
            easily accessed.</p>
    </aside>
</section>

<!-- About -->
<section id="about" class="card_layout">
    <div class="container" style="padding-top:20px; padding-bottom:20px;">
        <div class="smll_cd">
            <div class="cantBOX">
                <h4 style="margin:0 0 8px 0; color:var(--gov-deep);">কেন এই পোর্টাল?</h4>
                <p class="small">বিভিন্ন সরকারি সেবার জন্য বিভিন্ন সাইটে যেতে হয় — সময় নষ্ট ও অপচয় বৃদ্ধি
                    পায়। আমাদের লক্ষ্য নাগরিকদের জন্য একটি অভিন্ন, সুরক্ষিত ও সরকারি-অনুমোদিত উৎস তৈরি করা।</p>
            </div>
            <div class="cantBOX">
                <h4 style="margin:0 0 8px 0; color:var(--gov-deep);">লক্ষ্য</h4>
                <p class="small">ডিজিটালাইজেশন, কার্যকারিতা বৃদ্ধি, আর নাগরিকদের সার্ভিস দ্রুততর করা।</p>
            </div>
            <div class="cantBOX">
                <h4 style="margin:0 0 8px 0; color:var(--gov-deep);">গোপনীয়তা</h4>
                <p class="small">আপনার ডেটা নিরাপদ: এনক্রিপশন, রোল-বেসড অ্যাক্সেস ও ইউনিক কনসেন্ট ম্যানেজমেন্ট
                    ব্যবহার করা হবে।</p>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section id="how" style="padding:28px 0;">
    <div style="max-width:900px; margin:0 auto; text-align:center;">
        <h3 style="color:var(--gov-deep); margin-bottom:6px;">কিভাবে কাজ করে</h3>
        <p class="small">চেন না হলে উদ্বিগ্ন হওয়ার দরকার নেই — ৪টি সহজ ধাপে পুরো প্রক্রিয়া</p>

        <div class="process" style="max-width:980px; margin:18px auto 0;">
            <div class="step">
                <div class="num">1</div>
                <h4 style="margin:8px 0 6px 0;">Register</h4>
                <p class="small">NID দিয়ে দ্রুত পরিচয় যাচাই করে রেজিস্ট্রেশন করুন।</p>
            </div>
            <div class="step">
                <div class="num">2</div>
                <h4 style="margin:8px 0 6px 0;">Link</h4>
                <p class="small">সরকারী ডাটাবেসের মাধ্যমে আপনার ডকুমেন্টগুলো লিংক হবে বা আপলোড করুন।</p>
            </div>
            <div class="step">
                <div class="num">3</div>
                <h4 style="margin:8px 0 6px 0;">Verify</h4>
                <p class="small">Admin verification বা automated checks-এর মাধ্যমে তথ্য নিশ্চিত করা হবে।</p>
            </div>
            <div class="step">
                <div class="num">4</div>
                <h4 style="margin:8px 0 6px 0;">Access</h4>
                <p class="small">আপনি যে কোনও সময় ডকুমেন্ট ভিউ/ডাউনলোড করতে পারবেন।</p>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES -->
<section id="services" style="padding:24px 0; background:var(--card); border-top:1px solid #eef2f7;">
    <div style="max-width:1100px; margin:0 auto;">
        <h3 style="color:var(--gov-deep); margin-bottom:6px;">Available Documents / সেবা</h3>
        <p class="small">প্রাথমিকভাবে নিম্নলিখিত ডকুমেন্ট সাপোর্ট করা হবে।</p>

        <div class="service_G" style="margin-top:14px;">
            <div class="ser-cd">
                <h4 style="margin:0 0 6px 0;">NID (জাতীয় পরিচয়)</h4>
                <p class="small">প্রতিটি নাগরিক তার জাতীয় পরিচয় পত্র খুব সহজেই এক্সেস করতে পারবে।</p>
            </div>
            <div class="ser-cd">
                <h4 style="margin:0 0 6px 0;">Passport</h4>
                <p class="small">খুব সহজেই নাগরিক তার E-Passport এক্সেস করতে ।</p>
            </div>
            <div class="ser-cd">
                <h4 style="margin:0 0 6px 0;">Birth Certificate</h4>
                <p class="small">জন্ম নিবন্ধন তথ্য প্রদর্শন।</p>
            </div>
            <div class="ser-cd">
                <h4 style="margin:0 0 6px 0;">Driving Licence</h4>
                <p class="small">BRTA লাইসেন্স যাচাই ও মেয়াদ।</p>
            </div>
            <div class="ser-cd">
                <h4 style="margin:0 0 6px 0;">Education Certificates</h4>
                <p class="small">Board certificates & diplomas (future integration).</p>
            </div>
            <div class="ser-cd">
                <h4 style="margin:0 0 6px 0;">Vehicle Registration</h4>
                <p class="small">বাইক/গাড়ি রেজিস্ট্রেশন ডকুমেন্ট।</p>
            </div>
            <div class="ser-cd">
                <h4 style="margin:0 0 6px 0;">Family Linking</h4>
                <p class="small">Guardian & family members link করার সুবিধা।</p>
            </div>
            <div class="ser-cd">
                <h4 style="margin:0 0 6px 0;">Verified Downloads</h4>
                <p class="small">Watermarked & signed PDF downloads for official use.</p>
            </div>
        </div>
    </div>
</section>

<!-- SECURITY AND PRIVACY -->
<section id="security-container" style="padding:28px 0;">
    <div style="max-width:950px; margin:0 auto;">
        <h3 style="color:var(--gov-deep); text-align:center;">Security & Privacy</h3>
        <p class="small" style="text-align:center; margin-top:6px;">আপনার তথ্য নিরাপদে রাখা আমাদের প্রাথমিক
            দায়িত্ব।</p>

        <div class="security-cart" style="margin-top:14px;">
            <div class="sec-cart-box">
                <h4 style="margin:0 0 8px 0;">Encryption</h4>
                <p class="small">ব্যক্তিগত তথ্য সর্বোচ্চ নিরাপত্তা দেওয়া হবে। </p>
            </div>
            <div class="sec-cart-box">
                <h4 style="margin:0 0 8px 0;">Two-Factor Auth</h4>
                <p class="small">ওটিপি এবং ঐচ্ছিক বায়োমেট্রিক যাচাইকরণ।</p>
            </div>
            <div class="sec-cart-box">
                <h4 style="margin:0 0 8px 0;">Audit & Consent</h4>
                <p class="small">অ্যাক্সেস লগ & কনসেন্ট ম্যানেজমেন্ট থাকবে।</p>
            </div>
        </div>
    </div>
</section>

<!-- Govt departments -->
<section id="Department">
    <div class="department-container">
        <h3>Integrated Departments</h3>
        <p>প্রাথমিক পর্যায়ে নিম্নলিখিত সংস্থাগুলোর সাথে কনট্যাক্ট ও ইন্টিগ্রেশন পরিকল্পনা</p>

        <div class="department-logos">
            <div class="department-item">
                <img src="assets/images/EC_logo.png" alt="Election Commission">
            </div>
            <div class="department-item">
                <img src="assets/images/PO_logo.png" alt="Passport Office">
            </div>
            <div class="department-item">
                <img src="assets/images/brta_logo.png" alt="BRTA">
            </div>
            <div class="department-item">
                <img src="assets/images/edu_logo.png" alt="Education Board">
            </div>
            <div class="department-item">
                <img src="assets/images/ict_logo.png" alt="ICT Division">
            </div>
            <div class="department-item">
                <img src="assets/images/NDC_logo.jpg" alt="National Data Center">
            </div>
        </div>
    </div>
</section>

<!-- Feedback section -->
<section id="feedback" style="padding:28px 0;">
    <div style="max-width:950px; margin:0 auto;">
        <div class="feedback-cta">
            <div>
                <h3 style="margin:0 0 6px 0;">Give your feedback or complaint</h3>
                <p class="small" style="margin:0;">আপনার মূল্যবান মতামত অথবা অভিযোগ প্রদান করতে, নিচের ফর্মটি
                    পূরণ করুন।</p>
            </div>

            <form class="feedback-form" action="process_feedback.php" method="POST">  <!-- Processes to root file -->
                <input class="input" name="full_name" placeholder="Full name / নাম" required />
                <input class="input" name="phone" placeholder="Phone" required />
                <input class="input" name="email" placeholder="Email (optional)" />
                <textarea class="input" name="message" placeholder="আপনার বার্তা লিখুন" rows="1" required></textarea>

                <button class="feedback-btn" type="submit">Send Message</button>
            </form>
        </div>
    </div>
</section>


<?php

if (isset($_GET['msg'])) {
    echo '<script>alert("' . htmlspecialchars($_GET['msg']) . '");</script>';
}
require_once 'includes/footer.php';  
?>