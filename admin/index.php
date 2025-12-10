<?php
require_once '../includes/auth.php';
require_admin();

$message = '';

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $fb = $_POST['facebook_url'];
    $insta = $_POST['instagram_url'];
    $logo = $_POST['logo_url'];

    $update_sql = "INSERT INTO settings (setting_key, setting_value) VALUES 
        ('facebook_url', ?), ('instagram_url', ?), ('company_logo', ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
    
    // We need to do this one by one or careful construction for ON DUPLICATE with multiple rows logic 
    // Easier loop:
    $settings_to_update = [
        'facebook_url' => $fb,
        'instagram_url' => $insta,
        'company_logo' => $logo
    ];

    foreach ($settings_to_update as $key => $val) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $val, $val]);
    }
    $message = "تم تحديث الإعدادات بنجاح!";
}

// Fetch Logs
$logs_stmt = $pdo->query("SELECT * FROM access_logs ORDER BY login_time DESC LIMIT 100");
$logs = $logs_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Current Settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$current_settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم - الأدمن</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-nav {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .table-glass {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .table-glass th, .table-glass td {
            color: white;
            border-color: rgba(255, 255, 255, 0.2);
        }
        .tab-content {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 0 0 10px 10px;
        }
        .nav-tabs .nav-link {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            margin-left: 2px;
        }
        .nav-tabs .nav-link.active {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark admin-nav mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">لوحة تحكم الأدمن</span>
        <a href="../home.php" class="btn btn-sm btn-outline-light">العودة للرئيسية</a>
    </div>
</nav>

<div class="container">
    <?php if ($message): ?>
        <div class="alert alert-success text-center"><?php echo $message; ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab">سجل الدخول</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">الإعدادات</button>
        </li>
    </ul>

    <div class="tab-content glass-card mx-0 w-100" style="max-width: 100%;">
        <!-- Logs Tab -->
        <div class="tab-pane fade show active" id="logs" role="tabpanel">
            <h4 class="mb-4">سجل دخول الموظفين</h4>
            <div class="table-responsive">
                <table class="table table-glass text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الوقت</th>
                            <th>IP</th>
                            <th>الجهاز</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo $log['id']; ?></td>
                            <td><?php echo htmlspecialchars($log['employee_name']); ?></td>
                            <td><?php echo $log['login_time']; ?></td>
                            <td><?php echo $log['ip_address']; ?></td>
                            <td class="small"><?php echo htmlspecialchars($log['device_info']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Settings Tab -->
        <div class="tab-pane fade" id="settings" role="tabpanel">
            <h4 class="mb-4">إعدادات الموقع</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">رابط فيسبوك</label>
                    <input type="url" name="facebook_url" class="form-control" value="<?php echo htmlspecialchars($current_settings['facebook_url'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">رابط انستغرام</label>
                    <input type="url" name="instagram_url" class="form-control" value="<?php echo htmlspecialchars($current_settings['instagram_url'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">رابط اللوغو (صورة)</label>
                    <input type="url" name="logo_url" class="form-control" value="<?php echo htmlspecialchars($current_settings['company_logo'] ?? ''); ?>">
                    <small class="text-white-50">يمكنك وضع رابط مباشر للصورة هنا</small>
                </div>
                <button type="submit" name="update_settings" class="btn btn-custom">حفظ التغييرات</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
