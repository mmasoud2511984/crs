<?php
/**
 * File: index.php
 * Path: /car-rental-system/index.php
 * Purpose: نقطة الدخول الرئيسية للنظام - Main entry point of the application
 * Dependencies: composer autoload, .env configuration
 */

// بدء العد الزمني لقياس الأداء
define('START_TIME', microtime(true));

// تعريف المسار الرئيسي
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CORE_PATH', ROOT_PATH . '/core');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// تحميل composer autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// تحميل متغيرات البيئة
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

// إعدادات PHP الأساسية
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Riyadh');
ini_set('display_errors', $_ENV['APP_DEBUG'] === 'true' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_PATH . '/logs/error.log');

// مستوى الإبلاغ عن الأخطاء
error_reporting($_ENV['APP_DEBUG'] === 'true' ? E_ALL : E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// تعيين الترميز
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// معالج الأخطاء المخصص
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    
    $error = [
        'severity' => $severity,
        'message' => $message,
        'file' => $file,
        'line' => $line,
        'time' => date('Y-m-d H:i:s'),
        'url' => $_SERVER['REQUEST_URI'] ?? '',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    
    // تسجيل الخطأ
    error_log(json_encode($error, JSON_UNESCAPED_UNICODE), 3, STORAGE_PATH . '/logs/error.log');
    
    // عرض الخطأ في وضع التطوير فقط
    if ($_ENV['APP_DEBUG'] === 'true') {
        echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;padding:15px;margin:10px;border-radius:5px;'>";
        echo "<h3 style='color:#721c24;margin:0 0 10px 0;'>⚠️ Error</h3>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($message) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($file) . " (Line: $line)</p>";
        echo "</div>";
    }
    
    return true;
});

// معالج الاستثناءات المخصص
set_exception_handler(function($exception) {
    $error = [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString(),
        'time' => date('Y-m-d H:i:s'),
        'url' => $_SERVER['REQUEST_URI'] ?? '',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    
    // تسجيل الاستثناء
    error_log(json_encode($error, JSON_UNESCAPED_UNICODE), 3, STORAGE_PATH . '/logs/error.log');
    
    // عرض صفحة خطأ
    if ($_ENV['APP_DEBUG'] === 'true') {
        echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;padding:20px;margin:20px;border-radius:5px;font-family:Arial,sans-serif;'>";
        echo "<h2 style='color:#721c24;margin:0 0 15px 0;'>⚠️ Exception</h2>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . " (Line: " . $exception->getLine() . ")</p>";
        echo "<details><summary style='cursor:pointer;color:#721c24;font-weight:bold;margin-top:15px;'>Stack Trace</summary>";
        echo "<pre style='background:#fff;padding:10px;border:1px solid #ddd;margin-top:10px;overflow:auto;'>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        echo "</details></div>";
    } else {
        http_response_code(500);
        include APP_PATH . '/views/errors/500.php';
    }
});

// بدء الجلسة
session_start([
    'name' => 'CRMS_SESSION',
    'cookie_lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
    'cookie_path' => $_ENV['SESSION_PATH'] ?? '/',
    'cookie_domain' => $_ENV['SESSION_DOMAIN'] ?? '',
    'cookie_secure' => ($_ENV['SESSION_SECURE'] ?? 'false') === 'true',
    'cookie_httponly' => ($_ENV['SESSION_HTTP_ONLY'] ?? 'true') === 'true',
    'cookie_samesite' => $_ENV['SESSION_SAME_SITE'] ?? 'Lax',
    'use_strict_mode' => true,
    'use_only_cookies' => true,
]);

// تجديد معرف الجلسة بشكل دوري
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

try {
    // تحميل ملفات الإعداد
    require_once CONFIG_PATH . '/database.php';
    require_once CONFIG_PATH . '/app.php';
    
    // إنشاء كائن الموجه
    $router = new Core\Router();
    
    // الحصول على URL المطلوب
    $url = $_GET['url'] ?? 'home';
    
    // تشغيل الموجه
    $router->route($url);
    
} catch (Exception $e) {
    // تسجيل الخطأ
    error_log('Fatal Error: ' . $e->getMessage(), 3, STORAGE_PATH . '/logs/error.log');
    
    // عرض صفحة خطأ
    if ($_ENV['APP_DEBUG'] === 'true') {
        throw $e;
    } else {
        http_response_code(500);
        if (file_exists(APP_PATH . '/views/errors/500.php')) {
            include APP_PATH . '/views/errors/500.php';
        } else {
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title></head><body>';
            echo '<h1>حدث خطأ - An Error Occurred</h1>';
            echo '<p>نعتذر، حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى لاحقاً.</p>';
            echo '<p>Sorry, an unexpected error occurred. Please try again later.</p>';
            echo '</body></html>';
        }
    }
}

// حساب وقت التنفيذ (في وضع التطوير)
if ($_ENV['APP_DEBUG'] === 'true') {
    $execution_time = microtime(true) - START_TIME;
    $memory_usage = memory_get_peak_usage(true) / 1024 / 1024;
    
    echo "\n<!-- ⚡ Performance: ";
    echo "Execution Time: " . number_format($execution_time, 4) . "s | ";
    echo "Memory Usage: " . number_format($memory_usage, 2) . "MB ";
    echo "-->\n";
}