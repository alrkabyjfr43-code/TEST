<?php
// includes/auth.php
session_start();
require_once __DIR__ . '/../config/db.php';

function log_visit($name, $pdo) {
    if (empty($name)) return;
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $device = $_SERVER['HTTP_USER_AGENT'];
    
    $stmt = $pdo->prepare("INSERT INTO access_logs (employee_name, ip_address, device_info) VALUES (?, ?, ?)");
    $stmt->execute([$name, $ip, $device]);
    
    $_SESSION['employee_name'] = $name;
}

function verify_admin($code) {
    // Hardcoded admin code as per requirements
    // In a real production app, this should be hashed and stored in DB
    return $code === '313alsb3';
}

function is_admin_logged_in() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function require_admin() {
    if (!is_admin_logged_in()) {
        header("Location: ../index.php");
        exit;
    }
}
?>
