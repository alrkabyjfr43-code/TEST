-- Database Schema for Company Portal

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'employee') DEFAULT 'employee'
);

CREATE TABLE IF NOT EXISTS `access_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_name` VARCHAR(100) NOT NULL,
    `login_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `ip_address` VARCHAR(45),
    `device_info` TEXT
);

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(50) UNIQUE NOT NULL,
    `setting_value` TEXT
);

-- Insert Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('facebook_url', 'https://facebook.com'),
('instagram_url', 'https://instagram.com'),
('company_logo', 'https://karbalaholding.com/wp-content/uploads/2024/04/LOGO.jpeg')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);
