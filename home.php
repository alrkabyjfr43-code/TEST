<?php
require_once 'includes/auth.php';

// Fetch settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$facebook_url = $settings['facebook_url'] ?? '#';
$instagram_url = $settings['instagram_url'] ?? '#';
$logo_url = $settings['company_logo'] ?? 'https://karbalaholding.com/wp-content/uploads/2024/04/LOGO.jpeg';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الرئيسية - شركة المستشار المؤسسي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="container text-center">
        <!-- Logo -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-6" data-aos="fade-down" data-aos-duration="1200">
                <img src="<?php echo htmlspecialchars($logo_url); ?>" alt="Company Logo" class="img-fluid rounded-circle shadow-lg" style="max-height: 150px; border: 4px solid rgba(255,255,255,0.3);">
            </div>
        </div>

        <!-- Company Name -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8" data-aos="zoom-in" data-aos-delay="200">
                <h1 class="display-4 fw-bold text-white" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">شركة المستشار المؤسسي</h1>
            </div>
        </div>

        <!-- Social Icons -->
        <div class="row justify-content-center mb-5">
            <div class="col-auto" data-aos="fade-right" data-aos-delay="400">
                <a href="<?php echo htmlspecialchars($facebook_url); ?>" target="_blank" class="social-icon">
                    <i class="fab fa-facebook"></i>
                </a>
            </div>
            <div class="col-auto" data-aos="fade-left" data-aos-delay="600">
                <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>

        <!-- Developer Credit -->
        <div class="row justify-content-center mt-5 footer-credit">
            <div class="col-md-6 glass-card py-3" data-aos="flip-up" data-aos-delay="800">
                <p class="mb-0 text-white">
                    تطوير: <span class="fw-bold text-warning">جعفر صادق الركابي</span>
                </p>
            </div>
        </div>
        
        <div class="mt-4">
             <a href="index.php" class="btn btn-sm btn-outline-light">تسجيل خروج</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
