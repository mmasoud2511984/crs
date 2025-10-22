<?php
/**
 * File: quick-fix.php
 * Path: /install/quick-fix.php
 * Purpose: Quick fix for session issues
 * Usage: Run once then delete
 */

session_start();

// تهيئة Session بشكل صحيح
if (!isset($_SESSION['installer_lang'])) {
    $_SESSION['installer_lang'] = 'ar';
}

if (!isset($_SESSION['install_data'])) {
    $_SESSION['install_data'] = [];
}

// إضافة علامة أن الخطوة 1b تمت
$_SESSION['install_data']['step1b_completed'] = true;
$_SESSION['install_data']['folders_created'] = date('Y-m-d H:i:s');

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إصلاح سريع</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            text-align: center;
        }
        h1 { color: #10b981; margin-bottom: 20px; }
        .success { color: #10b981; font-size: 4rem; margin-bottom: 20px; }
        .info { background: #f0f9ff; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: right; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px;
            font-weight: bold;
        }
        .btn:hover { background: #1e40af; }
        code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success">✅</div>
        <h1>تم الإصلاح بنجاح!</h1>
        
        <div class="info">
            <h3>ما تم إصلاحه:</h3>
            <ul style="text-align: right; margin: 15px 0;">
                <li>✓ تهيئة <code>installer_lang</code> = ar</li>
                <li>✓ تهيئة <code>install_data</code></li>
                <li>✓ وضع علامة إكمال الخطوة 1b</li>
            </ul>
        </div>

        <h3>معلومات Session:</h3>
        <div class="info" style="text-align: left; direction: ltr; font-family: monospace; font-size: 12px;">
            <pre><?php print_r($_SESSION); ?></pre>
        </div>

        <p><strong>الآن يمكنك:</strong></p>
        <a href="?step=1b" class="btn">الخطوة 1b</a>
        <a href="?step=2" class="btn" style="background: #10b981;">الخطوة 2</a>
        
        <p style="margin-top: 30px; color: #ef4444;">
            <small>⚠️ احذف هذا الملف بعد الاستخدام!</small>
        </p>
    </div>
</body>
</html>