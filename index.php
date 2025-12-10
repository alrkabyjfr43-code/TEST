<?php
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['admin_code']) && !empty($_POST['admin_code'])) {
        if (verify_admin($_POST['admin_code'])) {
            $_SESSION['is_admin'] = true;
            header("Location: admin/index.php");
            exit;
        } else {
            $error = "كود الأدمن غير صحيح";
        }
    } elseif (isset($_POST['employee_name'])) {
        log_visit($_POST['employee_name'], $pdo);
        header("Location: home.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - شركة المستشار المؤسسي</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .secret-area {
            position: fixed;
            top: 10px;
            right: 10px; /* RTL, so right is start */
            cursor: pointer;
            width: 30px;
            height: 30px;
            z-index: 1000;
        }
    </style>
</head>
<body>

    <div class="glass-card" data-aos="zoom-in" data-aos-duration="1000">
        <div class="mb-4">
            <img src="https://karbalaholding.com/wp-content/uploads/2024/04/LOGO.jpeg" alt="Logo" style="width: 100px; border-radius: 50%; box-shadow: 0 0 15px rgba(0,0,0,0.2);">
        </div>
        <h2 class="mb-4">مرحباً بكم</h2>
        <p class="mb-4">شركة المستشار المؤسسي</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <input type="text" name="employee_name" class="form-control text-center" placeholder="أدخل اسمك للدخول" required>
            </div>
            
            <button type="submit" class="btn btn-custom w-100">دخول</button>

            <!-- Admin Logic Hidden -->
            <!-- Trigger hidden logic via specific action or hidden input if user knows UI trick, 
                 but requirement says 'Secret field for admin code'. 
                 I'll make a toggle or a constantly available but hidden input. -->
        </form>
    </div>

    <!-- Hidden Admin Login Overlay/Toggle -->
    <div class="secret-area" onclick="document.getElementById('adminModal').style.display='flex'">
        <i style="color: rgba(255,255,255,0.1);">⚙️</i> 
    </div>

    <div id="adminModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); align-items:center; justify-content:center; z-index:2000;">
        <div class="glass-card" style="max-width: 300px;">
            <h4 class="mb-3">دخول الأدمن</h4>
            <form method="POST" action="">
                <input type="password" name="admin_code" class="form-control mb-3 text-center" placeholder="كود الأدمن">
                <button type="submit" class="btn btn-danger w-100">تحقق</button>
                <button type="button" class="btn btn-secondary w-100 mt-2" onclick="document.getElementById('adminModal').style.display='none'">إلغاء</button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
