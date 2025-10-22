<?php
/**
 * File: debug.php
 * Path: /install/debug.php
 * Purpose: Debug installation issues
 * Usage: Open in browser to see session and server info
 */

session_start();

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 Installation Debugger</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #00ff00;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #2d2d2d;
            border-radius: 8px;
            padding: 30px;
        }
        h1 { color: #00ffff; margin-bottom: 20px; }
        h2 { color: #ffff00; margin: 20px 0 10px 0; border-bottom: 2px solid #ffff00; padding-bottom: 5px; }
        .section {
            background: #1a1a1a;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
            border-left: 4px solid #00ff00;
        }
        .good { color: #00ff00; }
        .bad { color: #ff0000; }
        .warning { color: #ff9900; }
        .info { color: #00ffff; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 10px;
            text-align: right;
            border: 1px solid #444;
        }
        th {
            background: #333;
            color: #ffff00;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #00ff00;
            color: #000;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
            font-weight: bold;
        }
        .btn:hover { background: #00cc00; }
        .btn-danger {
            background: #ff0000;
            color: #fff;
        }
        .btn-danger:hover { background: #cc0000; }
        code {
            background: #000;
            padding: 2px 6px;
            border-radius: 3px;
            color: #0ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 معالج تصحيح التثبيت</h1>
        <p class="info">هذا الملف للمساعدة في تشخيص مشاكل التثبيت</p>
        
        <!-- معلومات الخطوة الحالية -->
        <div class="section">
            <h2>📍 الخطوة الحالية</h2>
            <p><strong>URL:</strong> <code><?php echo $_SERVER['REQUEST_URI']; ?></code></p>
            <p><strong>Step Parameter:</strong> <code><?php echo $_GET['step'] ?? 'not set'; ?></code></p>
            <p><strong>Language Parameter:</strong> <code><?php echo $_GET['lang'] ?? 'not set'; ?></code></p>
        </div>

        <!-- معلومات الجلسة -->
        <div class="section">
            <h2>🗂️ Session Data</h2>
            <?php if (empty($_SESSION)): ?>
                <p class="bad">⚠️ Session فارغة!</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Key</th>
                        <th>Value</th>
                    </tr>
                    <?php foreach ($_SESSION as $key => $value): ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($key); ?></code></td>
                        <td>
                            <?php 
                            if (is_array($value)) {
                                echo '<pre>' . htmlspecialchars(print_r($value, true)) . '</pre>';
                            } else {
                                echo '<code>' . htmlspecialchars($value) . '</code>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
            
            <p class="<?php echo isset($_SESSION['installer_lang']) ? 'good' : 'bad'; ?>">
                <strong>installer_lang:</strong> 
                <?php echo isset($_SESSION['installer_lang']) ? '✓ ' . $_SESSION['installer_lang'] : '✗ غير محفوظة'; ?>
            </p>
        </div>

        <!-- معلومات PHP -->
        <div class="section">
            <h2>⚙️ PHP Configuration</h2>
            <table>
                <tr>
                    <td>PHP Version</td>
                    <td class="<?php echo version_compare(PHP_VERSION, '8.2.0', '>=') ? 'good' : 'bad'; ?>">
                        <?php echo PHP_VERSION; ?>
                    </td>
                </tr>
                <tr>
                    <td>Session Save Path</td>
                    <td><code><?php echo session_save_path(); ?></code></td>
                </tr>
                <tr>
                    <td>Session ID</td>
                    <td><code><?php echo session_id(); ?></code></td>
                </tr>
                <tr>
                    <td>Session Status</td>
                    <td class="<?php echo session_status() === PHP_SESSION_ACTIVE ? 'good' : 'bad'; ?>">
                        <?php 
                        switch(session_status()) {
                            case PHP_SESSION_DISABLED: echo 'DISABLED'; break;
                            case PHP_SESSION_NONE: echo 'NONE'; break;
                            case PHP_SESSION_ACTIVE: echo 'ACTIVE ✓'; break;
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- اختبار Session -->
        <div class="section">
            <h2>🧪 Session Test</h2>
            <?php
            // اختبار كتابة Session
            $_SESSION['test_write'] = date('Y-m-d H:i:s');
            $canWrite = isset($_SESSION['test_write']);
            ?>
            <p class="<?php echo $canWrite ? 'good' : 'bad'; ?>">
                <strong>كتابة Session:</strong> 
                <?php echo $canWrite ? '✓ تعمل' : '✗ لا تعمل'; ?>
            </p>
            <?php if ($canWrite): ?>
            <p><small>آخر اختبار: <code><?php echo $_SESSION['test_write']; ?></code></small></p>
            <?php endif; ?>
        </div>

        <!-- مسار التنقل الصحيح -->
        <div class="section">
            <h2>🎯 مسار التنقل الصحيح</h2>
            <table>
                <tr>
                    <th>الخطوة</th>
                    <th>الوصف</th>
                    <th>الرابط</th>
                </tr>
                <tr>
                    <td>0</td>
                    <td>اختيار اللغة</td>
                    <td><a href="?step=0" class="btn">Go</a></td>
                </tr>
                <tr>
                    <td>1b</td>
                    <td>إنشاء المجلدات</td>
                    <td><a href="?step=1b" class="btn">Go</a></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>قاعدة البيانات</td>
                    <td><a href="?step=2" class="btn">Go</a></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>معلومات الشركة</td>
                    <td><a href="?step=3" class="btn">Go</a></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>حساب المدير</td>
                    <td><a href="?step=4" class="btn">Go</a></td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>إعدادات اللغة</td>
                    <td><a href="?step=5" class="btn">Go</a></td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>الإنهاء</td>
                    <td><a href="?step=6" class="btn">Go</a></td>
                </tr>
            </table>
        </div>

        <!-- المشاكل المحتملة -->
        <div class="section">
            <h2>⚠️ المشاكل المحتملة</h2>
            <ul style="list-style: none; padding: 0;">
                <?php if (!isset($_SESSION['installer_lang'])): ?>
                <li class="bad">✗ اللغة غير محفوظة في Session</li>
                <li class="warning">الحل: اذهب للخطوة 0 واختر اللغة مرة أخرى</li>
                <?php endif; ?>
                
                <?php if (session_status() !== PHP_SESSION_ACTIVE): ?>
                <li class="bad">✗ Session غير نشطة</li>
                <li class="warning">الحل: تأكد من session_start() في index.php</li>
                <?php endif; ?>
                
                <?php if (!is_writable(session_save_path())): ?>
                <li class="bad">✗ مجلد Session غير قابل للكتابة</li>
                <li class="warning">الحل: اضبط أذونات <?php echo session_save_path(); ?></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- إجراءات سريعة -->
        <div class="section">
            <h2>⚡ إجراءات سريعة</h2>
            <a href="?step=0" class="btn">العودة للخطوة 0</a>
            <a href="?step=0&lang=ar" class="btn">اختيار العربية</a>
            <a href="?step=0&lang=en" class="btn">اختيار الإنجليزية</a>
            <a href="?clear=1" class="btn btn-danger">مسح Session</a>
            <a href="../index.php" class="btn">الصفحة الرئيسية</a>
        </div>

        <?php
        // مسح Session إذا طُلب
        if (isset($_GET['clear'])) {
            session_destroy();
            echo '<div class="section"><p class="good">✓ تم مسح Session. <a href="debug.php">أعد تحميل الصفحة</a></p></div>';
        }
        ?>

        <!-- تعليمات -->
        <div class="section">
            <h2>📖 تعليمات الاستخدام</h2>
            <ol style="padding-right: 20px;">
                <li>تحقق من أن <code>installer_lang</code> محفوظة في Session</li>
                <li>إذا لم تكن محفوظة، اذهب للخطوة 0 واختر اللغة</li>
                <li>تأكد من أن Session Status = <span class="good">ACTIVE</span></li>
                <li>جرب الروابط في "مسار التنقل الصحيح"</li>
                <li>إذا استمرت المشكلة، اضغط "مسح Session" وابدأ من جديد</li>
            </ol>
        </div>

        <p style="margin-top: 30px; text-align: center; color: #666;">
            <small>⚠️ احذف هذا الملف بعد حل المشكلة!</small>
        </p>
    </div>
</body>
</html>