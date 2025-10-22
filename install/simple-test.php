<?php
/**
 * Simple Database Test
 * Upload to: /install/simple-test.php
 * DELETE AFTER TESTING!
 */

// إظهار جميع الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

echo '<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اختبار بسيط</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #3b82f6; }
        .success { border-color: #10b981; background: #f0fdf4; }
        .error { border-color: #ef4444; background: #fef2f2; }
        .warning { border-color: #f59e0b; background: #fffbeb; }
        input, button { padding: 10px; margin: 5px 0; width: 100%; font-size: 14px; }
        button { background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2563eb; }
        pre { background: #1e1e1e; color: #0f0; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h2 { color: #1f2937; }
    </style>
</head>
<body>';

echo '<h1>🔧 اختبار قاعدة البيانات البسيط</h1>';

// معلومات PHP
echo '<div class="box">';
echo '<h2>1️⃣ معلومات PHP</h2>';
echo '<pre>';
echo 'PHP Version: ' . PHP_VERSION . "\n";
echo 'PDO Available: ' . (extension_loaded('pdo') ? '✅ YES' : '❌ NO') . "\n";
echo 'PDO MySQL Available: ' . (extension_loaded('pdo_mysql') ? '✅ YES' : '❌ NO') . "\n";
echo 'Display Errors: ' . ini_get('display_errors') . "\n";
echo 'Error Reporting: ' . error_reporting() . "\n";
echo '</pre>';
echo '</div>';

// معلومات الطلب
echo '<div class="box">';
echo '<h2>2️⃣ معلومات الطلب</h2>';
echo '<pre>';
echo 'Request Method: ' . $_SERVER['REQUEST_METHOD'] . "\n";
echo 'Script Name: ' . $_SERVER['SCRIPT_NAME'] . "\n";
echo 'Current Time: ' . date('Y-m-d H:i:s') . "\n";
echo '</pre>';
echo '</div>';

// إذا كان POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<div class="box warning">';
    echo '<h2>3️⃣ البيانات المستلمة</h2>';
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    echo '</div>';
    
    // محاولة الاتصال
    $prefix = trim($_POST['prefix'] ?? '');
    $host = trim($_POST['host'] ?? '');
    $port = trim($_POST['port'] ?? '3306');
    $dbname = trim($_POST['dbname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $fullDbName = $prefix . $dbname;
    $fullUsername = $prefix . $username;
    
    echo '<div class="box">';
    echo '<h2>4️⃣ محاولة الاتصال</h2>';
    echo '<pre>';
    echo "Host: $host\n";
    echo "Port: $port\n";
    echo "Full Database Name: $fullDbName\n";
    echo "Full Username: $fullUsername\n";
    echo "Password: " . (empty($password) ? '(empty)' : '(provided)') . "\n";
    echo "\n--- بدء الاتصال ---\n\n";
    
    try {
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        echo "DSN: $dsn\n";
        echo "Attempting connection...\n";
        
        $pdo = new PDO($dsn, $fullUsername, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ اتصال ناجح!\n\n";
        
        // معلومات MySQL
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        echo "MySQL Version: $version\n\n";
        
        // فحص قواعد البيانات
        echo "--- قواعد البيانات الموجودة ---\n";
        $databases = $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($databases as $db) {
            echo "- $db";
            if ($db === $fullDbName) echo " ← هذه قاعدتك!";
            echo "\n";
        }
        echo "\n";
        
        // فحص وجود القاعدة المطلوبة
        $stmt = $pdo->query("SHOW DATABASES LIKE '$fullDbName'");
        if ($stmt->rowCount() > 0) {
            echo "✅ قاعدة البيانات '$fullDbName' موجودة\n";
            
            // الاتصال بها
            $pdo->exec("USE `$fullDbName`");
            echo "✅ تم الاتصال بقاعدة البيانات\n\n";
            
            // عرض الجداول
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo "عدد الجداول: " . count($tables) . "\n";
            if (count($tables) > 0) {
                echo "الجداول:\n";
                foreach ($tables as $table) {
                    echo "  - $table\n";
                }
            }
        } else {
            echo "⚠️ قاعدة البيانات '$fullDbName' غير موجودة\n";
            echo "محاولة إنشائها...\n";
            
            try {
                $pdo->exec("CREATE DATABASE `$fullDbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                echo "✅ تم إنشاء قاعدة البيانات بنجاح!\n";
            } catch (PDOException $e) {
                echo "❌ فشل إنشاء قاعدة البيانات: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n✅ الاختبار نجح بالكامل!\n";
        echo '</pre>';
        echo '</div>';
        
        echo '<div class="box success">';
        echo '<h2>🎉 النتيجة النهائية</h2>';
        echo '<p style="font-size: 18px; color: #059669;">✅ <strong>الاتصال ناجح!</strong> يمكنك المتابعة للتثبيت</p>';
        echo '<p>استخدم هذه البيانات في المثبت:</p>';
        echo '<ul>';
        echo '<li>البادئة: <code>' . htmlspecialchars($prefix ?: '(فارغة)') . '</code></li>';
        echo '<li>المضيف: <code>' . htmlspecialchars($host) . '</code></li>';
        echo '<li>اسم القاعدة: <code>' . htmlspecialchars($dbname) . '</code></li>';
        echo '<li>اسم المستخدم: <code>' . htmlspecialchars($username) . '</code></li>';
        echo '<li>الاسم الكامل للقاعدة: <code>' . htmlspecialchars($fullDbName) . '</code></li>';
        echo '<li>الاسم الكامل للمستخدم: <code>' . htmlspecialchars($fullUsername) . '</code></li>';
        echo '</ul>';
        echo '</div>';
        
    } catch (PDOException $e) {
        echo "❌ خطأ: " . $e->getMessage() . "\n";
        echo "\nتفاصيل الخطأ:\n";
        echo "Error Code: " . $e->getCode() . "\n";
        echo "Error Info: ";
        print_r($e->errorInfo);
        echo '</pre>';
        echo '</div>';
        
        echo '<div class="box error">';
        echo '<h2>❌ فشل الاتصال</h2>';
        
        $errorMsg = $e->getMessage();
        if (strpos($errorMsg, 'Access denied') !== false) {
            echo '<p><strong>المشكلة:</strong> اسم المستخدم أو كلمة المرور غير صحيحة</p>';
            echo '<p><strong>الحلول:</strong></p>';
            echo '<ul>';
            echo '<li>تأكد من اسم المستخدم: <code>' . htmlspecialchars($fullUsername) . '</code></li>';
            echo '<li>تأكد من كلمة المرور</li>';
            echo '<li>تأكد من أن المستخدم موجود في cPanel</li>';
            echo '</ul>';
        } elseif (strpos($errorMsg, "Can't connect") !== false) {
            echo '<p><strong>المشكلة:</strong> لا يمكن الاتصال بالمضيف</p>';
            echo '<p><strong>الحلول:</strong></p>';
            echo '<ul>';
            echo '<li>جرب استخدام <code>localhost</code> بدلاً من <code>127.0.0.1</code></li>';
            echo '<li>تأكد من أن MySQL يعمل على المنفذ ' . htmlspecialchars($port) . '</li>';
            echo '<li>تحقق من إعدادات الـ firewall</li>';
            echo '</ul>';
        } else {
            echo '<p><strong>الخطأ:</strong> ' . htmlspecialchars($errorMsg) . '</p>';
        }
        echo '</div>';
    }
}

// النموذج
echo '<div class="box">';
echo '<h2>📝 نموذج الاختبار</h2>';
echo '<form method="POST">';
echo '<label>البادئة (Prefix) - للـ cPanel فقط</label>';
echo '<input type="text" name="prefix" value="' . htmlspecialchars($_POST['prefix'] ?? '') . '" placeholder="u995861180_">';

echo '<label>المضيف *</label>';
echo '<input type="text" name="host" value="' . htmlspecialchars($_POST['host'] ?? 'localhost') . '" required>';

echo '<label>المنفذ</label>';
echo '<input type="text" name="port" value="' . htmlspecialchars($_POST['port'] ?? '3306') . '">';

echo '<label>اسم قاعدة البيانات (بدون البادئة) *</label>';
echo '<input type="text" name="dbname" value="' . htmlspecialchars($_POST['dbname'] ?? '') . '" placeholder="car_rental" required>';

echo '<label>اسم المستخدم (بدون البادئة) *</label>';
echo '<input type="text" name="username" value="' . htmlspecialchars($_POST['username'] ?? '') . '" placeholder="info" required>';

echo '<label>كلمة المرور</label>';
echo '<input type="password" name="password" value="' . htmlspecialchars($_POST['password'] ?? '') . '">';

echo '<button type="submit">🔍 اختبار الاتصال</button>';
echo '</form>';
echo '</div>';

echo '<div class="box error">';
echo '<h2>⚠️ تحذير</h2>';
echo '<p><strong>احذف هذا الملف فوراً بعد الاختبار!</strong></p>';
echo '<p>هذا الملف يعرض معلومات حساسة عن الخادم</p>';
echo '</div>';

echo '<script>
console.log("✅ JavaScript يعمل");
console.log("الصفحة الحالية:", window.location.href);

// عند الإرسال
document.querySelector("form")?.addEventListener("submit", function(e) {
    console.log("✅ النموذج تم إرساله");
    console.log("البيانات:", new FormData(this));
    
    // تعطيل الزر لمنع الإرسال المتكرر
    const btn = this.querySelector("button");
    btn.disabled = true;
    btn.textContent = "⏳ جاري الاختبار...";
});

// عند تحميل الصفحة
window.addEventListener("load", function() {
    console.log("✅ الصفحة تم تحميلها بالكامل");
});
</script>';

echo '</body></html>';
?>
