<?php
/**
 * Database Pool Generator
 * Path: /install/generate-pool.php
 * Password: lkhfd#$hf!ljlH97%&6~9#D
 * WARNING: DELETE THIS FILE IMMEDIATELY AFTER USE!
 */

define('FIXED_PASSWORD', 'lkhfd#$hf!ljlH97%&6~9#D');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 Database Pool Generator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 { font-size: 2rem; margin-bottom: 10px; }
        .content { padding: 40px; }
        .form-group { margin-bottom: 25px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .btn:hover { transform: translateY(-2px); }
        .success {
            background: #f0fdf4;
            border: 2px solid #10b981;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .warning {
            background: #fef2f2;
            border: 2px solid #ef4444;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            color: #991b1b;
        }
        .info {
            background: #eff6ff;
            border: 2px solid #3b82f6;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
        code {
            background: #f3f4f6;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        textarea {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            min-height: 120px;
            resize: vertical;
        }
        .copy-btn {
            display: inline-block;
            padding: 8px 16px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin: 5px;
        }
        .copy-btn:hover { background: #2563eb; }
        small {
            display: block;
            margin-top: 5px;
            color: #6b7280;
            font-size: 13px;
        }
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .content { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏊 Database Pool Generator</h1>
            <p>إنشاء 50 قاعدة بيانات بكلمة مرور موحدة</p>
        </div>
        
        <div class="content">
            <div class="warning">
                <h3 style="margin-bottom: 10px;">🔥 تحذير أمني حرج!</h3>
                <p style="font-size: 16px; font-weight: bold;">احذف هذا الملف فوراً بعد الانتهاء!</p>
                <code style="display: block; margin-top: 10px;">rm generate-pool.php</code>
            </div>

            <div class="info">
                <h3 style="margin-bottom: 10px;">🔐 كلمة المرور الثابتة</h3>
                <div style="font-family: monospace; background: #1e1e1e; color: #0f0; padding: 15px; border-radius: 6px; font-size: 16px; letter-spacing: 1px;">
                    <?php echo htmlspecialchars(FIXED_PASSWORD); ?>
                </div>
                <p style="margin-top: 10px; color: #1e40af;">
                    ✅ سيتم استخدام هذه الكلمة لجميع القواعد
                </p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>البادئة (Prefix) *</label>
                    <input type="text" 
                           name="prefix" 
                           value="<?php echo htmlspecialchars($_POST['prefix'] ?? 'u995861180_'); ?>" 
                           required>
                    <small>البادئة الإجبارية من hPanel</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>المضيف *</label>
                        <input type="text" 
                               name="host" 
                               value="<?php echo htmlspecialchars($_POST['host'] ?? 'localhost'); ?>" 
                               required>
                    </div>
                    <div class="form-group">
                        <label>المنفذ</label>
                        <input type="text" 
                               name="port" 
                               value="<?php echo htmlspecialchars($_POST['port'] ?? '3306'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>عدد القواعد</label>
                    <input type="number" 
                           name="count" 
                           value="<?php echo htmlspecialchars($_POST['count'] ?? '50'); ?>" 
                           min="1" max="100" 
                           required>
                    <small>من 1 إلى 100 قاعدة بيانات</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>قالب اسم القاعدة *</label>
                        <input type="text" 
                               name="db_pattern" 
                               value="<?php echo htmlspecialchars($_POST['db_pattern'] ?? 'carrent_{n}'); ?>" 
                               required>
                        <small>استخدم {n} للرقم التسلسلي</small>
                    </div>
                    <div class="form-group">
                        <label>قالب المستخدم *</label>
                        <input type="text" 
                               name="user_pattern" 
                               value="<?php echo htmlspecialchars($_POST['user_pattern'] ?? 'user_{n}'); ?>" 
                               required>
                        <small>استخدم {n} للرقم</small>
                    </div>
                </div>

                <button type="submit" class="btn">🔧 إنشاء ملف databases.json</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $prefix = trim($_POST['prefix']);
                $host = trim($_POST['host']);
                $port = trim($_POST['port']);
                $count = (int)$_POST['count'];
                $dbPattern = trim($_POST['db_pattern']);
                $userPattern = trim($_POST['user_pattern']);
                $password = FIXED_PASSWORD;

                $databases = [];
                
                for ($i = 1; $i <= $count; $i++) {
                    $n = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $dbname = str_replace('{n}', $n, $dbPattern);
                    $username = str_replace('{n}', $n, $userPattern);
                    
                    $databases[] = [
                        'id' => $i,
                        'prefix' => $prefix,
                        'dbname' => $dbname,
                        'username' => $username,
                        'password' => $password,
                        'status' => 'available',
                        'reserved_at' => null,
                        'reserved_by' => null
                    ];
                }

                $data = [
                    'databases' => $databases,
                    'settings' => [
                        'host' => $host,
                        'port' => $port,
                        'auto_release_after_hours' => 24,
                        'unified_password' => true,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                ];

                $configDir = __DIR__ . '/config';
                if (!is_dir($configDir)) {
                    mkdir($configDir, 0755, true);
                }

                $configFile = $configDir . '/databases.json';
                file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                chmod($configFile, 0600);

                $htaccess = $configDir . '/.htaccess';
                $htaccessContent = "# حماية ملفات التكوين\n<Files \"*.json\">\n    Order Allow,Deny\n    Deny from all\n</Files>";
                file_put_contents($htaccess, $htaccessContent);

                echo '<div class="success">';
                echo '<h3>✅ تم إنشاء الملف بنجاح!</h3>';
                echo '<p><strong>الموقع:</strong> <code>' . $configFile . '</code></p>';
                echo '<p><strong>عدد القواعد:</strong> ' . $count . '</p>';
                echo '</div>';

                echo '<div class="info">';
                echo '<h3>📝 الخطوات في hPanel</h3>';
                echo '<ol style="line-height: 2;">';
                echo '<li>اذهب إلى hPanel → MySQL Databases</li>';
                echo '<li>أنشئ ' . $count . ' قاعدة (انسخ القائمة أدناه)</li>';
                echo '<li>أنشئ ' . $count . ' مستخدم (انسخ القائمة أدناه)</li>';
                echo '<li>كلمة المرور لكل واحد: <code>' . htmlspecialchars($password) . '</code></li>';
                echo '<li>اربط كل مستخدم بقاعدته مع ALL PRIVILEGES</li>';
                echo '</ol>';
                echo '</div>';

                echo '<div class="form-group">';
                echo '<label>📋 أسماء القواعد</label>';
                echo '<textarea id="dbList" readonly>';
                foreach ($databases as $db) {
                    echo $prefix . $db['dbname'] . "\n";
                }
                echo '</textarea>';
                echo '<button class="copy-btn" onclick="copy(\'dbList\')">📋 نسخ</button>';
                echo '</div>';

                echo '<div class="form-group">';
                echo '<label>👤 أسماء المستخدمين</label>';
                echo '<textarea id="userList" readonly>';
                foreach ($databases as $db) {
                    echo $prefix . $db['username'] . "\n";
                }
                echo '</textarea>';
                echo '<button class="copy-btn" onclick="copy(\'userList\')">📋 نسخ</button>';
                echo '</div>';

                echo '<div class="form-group">';
                echo '<label>🔐 كلمة المرور (انسخها ' . $count . ' مرة)</label>';
                echo '<textarea id="password" readonly>' . htmlspecialchars($password) . '</textarea>';
                echo '<button class="copy-btn" onclick="copy(\'password\')">📋 نسخ</button>';
                echo '</div>';

                echo '<div class="warning">';
                echo '<h3>🔥 الآن احذف هذا الملف!</h3>';
                echo '<code>rm generate-pool.php</code>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <script>
    function copy(id) {
        const el = document.getElementById(id);
        el.select();
        el.setSelectionRange(0, 99999);
        document.execCommand('copy');
        alert('✅ تم النسخ!');
    }
    </script>
</body>
</html>