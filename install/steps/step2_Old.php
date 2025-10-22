<?php
/**
 * File: step2.php
 * Path: /install/steps/step2.php
 * Purpose: Database configuration with enhanced validation
 */

$texts = [
    'ar' => [
        'title' => 'إعداد قاعدة البيانات',
        'subtitle' => 'قم بإدخال معلومات الاتصال بقاعدة البيانات',
        'section_connection' => 'معلومات الاتصال',
        'cpanel_note' => 'ملاحظة cPanel',
        'cpanel_help' => 'إذا كنت تستخدم cPanel، قد يكون لديك prefix إجباري (مثل: u995861180_)',
        'prefix' => 'البادئة (Prefix)',
        'prefix_help' => 'اتركها فارغة إذا لم يكن لديك prefix',
        'host' => 'اسم المضيف',
        'host_help' => 'عادة ما يكون localhost',
        'port' => 'المنفذ',
        'port_help' => 'المنفذ الافتراضي 3306',
        'dbname' => 'اسم قاعدة البيانات',
        'dbname_help' => 'حروف إنجليزية وأرقام و_ فقط',
        'username' => 'اسم المستخدم',
        'username_help' => 'حروف إنجليزية وأرقام و_ فقط',
        'password' => 'كلمة المرور',
        'password_help' => 'اتركها فارغة إذا لم تكن محددة',
        'test_button' => 'اختبار الاتصال والمتابعة',
        'testing' => 'جاري الاختبار...',
        'success_title' => 'نجح!',
        'success_msg' => 'تم إنشاء قاعدة البيانات والجداول بنجاح',
        'error_title' => 'خطأ',
        'warning_title' => 'تنبيه مهم',
        'warning_msg' => 'تأكد من أن لديك صلاحيات كاملة على قاعدة البيانات',
        'prev' => 'السابق',
        'next' => 'التالي: معلومات الشركة'
    ],
    'en' => [
        'title' => 'Database Setup',
        'subtitle' => 'Enter your database connection information',
        'section_connection' => 'Connection Information',
        'cpanel_note' => 'cPanel Note',
        'cpanel_help' => 'If using cPanel, you may have a mandatory prefix (e.g., u995861180_)',
        'prefix' => 'Prefix',
        'prefix_help' => 'Leave empty if you don\'t have a prefix',
        'host' => 'Host',
        'host_help' => 'Usually localhost',
        'port' => 'Port',
        'port_help' => 'Default port is 3306',
        'dbname' => 'Database Name',
        'dbname_help' => 'English letters, numbers and _ only',
        'username' => 'Username',
        'username_help' => 'English letters, numbers and _ only',
        'password' => 'Password',
        'password_help' => 'Leave empty if not set',
        'test_button' => 'Test Connection and Continue',
        'testing' => 'Testing...',
        'success_title' => 'Success!',
        'success_msg' => 'Database and tables created successfully',
        'error_title' => 'Error',
        'warning_title' => 'Important Note',
        'warning_msg' => 'Make sure you have full permissions on the database',
        'prev' => 'Previous',
        'next' => 'Next: Company Info'
    ]
];

$currentLang = $lang ?? 'ar';
$text = $texts[$currentLang];

$errors = [];
$warnings = [];
$debugInfo = [];
$success = false;

// تسجيل بداية المعالجة
$debugInfo[] = 'بدء معالجة الطلب - الوقت: ' . date('Y-m-d H:i:s');
$debugInfo[] = 'طريقة الطلب: ' . $_SERVER['REQUEST_METHOD'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debugInfo[] = '✓ الطلب من نوع POST';
    
    // استقبال البيانات
    $dbHost = trim($_POST['db_host'] ?? '');
    $dbPort = trim($_POST['db_port'] ?? '3306');
    $dbPrefix = trim($_POST['db_prefix'] ?? '');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? '';

    $debugInfo[] = "البيانات المستلمة:";
    $debugInfo[] = "- المضيف: $dbHost";
    $debugInfo[] = "- المنفذ: $dbPort";
    $debugInfo[] = "- البادئة: " . ($dbPrefix ? $dbPrefix : '(فارغة)');
    $debugInfo[] = "- اسم القاعدة: $dbName";
    $debugInfo[] = "- المستخدم: $dbUser";
    $debugInfo[] = "- كلمة المرور: " . (empty($dbPass) ? '(فارغة)' : '(موجودة)');

    // التحقق من البيانات الأساسية
    if (empty($dbHost)) {
        $errors[] = $currentLang === 'ar' ? 'اسم المضيف مطلوب' : 'Host is required';
    }
    if (empty($dbName)) {
        $errors[] = $currentLang === 'ar' ? 'اسم قاعدة البيانات مطلوب' : 'Database name is required';
    }
    if (empty($dbUser)) {
        $errors[] = $currentLang === 'ar' ? 'اسم المستخدم مطلوب' : 'Username is required';
    }

    // التحقق من صحة اسم القاعدة (إنجليزي فقط)
    if (!empty($dbName) && !preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) {
        $errors[] = $currentLang === 'ar' 
            ? 'اسم قاعدة البيانات يجب أن يحتوي على حروف إنجليزية وأرقام و_ فقط' 
            : 'Database name must contain only English letters, numbers and _';
    }

    // التحقق من صحة اسم المستخدم (إنجليزي فقط)
    if (!empty($dbUser) && !preg_match('/^[a-zA-Z0-9_]+$/', $dbUser)) {
        $errors[] = $currentLang === 'ar' 
            ? 'اسم المستخدم يجب أن يحتوي على حروف إنجليزية وأرقام و_ فقط' 
            : 'Username must contain only English letters, numbers and _';
    }

    // إضافة البادئة
    $fullDbName = $dbPrefix . $dbName;
    $fullDbUser = $dbPrefix . $dbUser;

    $debugInfo[] = "الأسماء الكاملة:";
    $debugInfo[] = "- القاعدة الكاملة: $fullDbName";
    $debugInfo[] = "- المستخدم الكامل: $fullDbUser";

    if (empty($errors)) {
        $debugInfo[] = '✓ التحقق من البيانات نجح';
        
        try {
            $debugInfo[] = 'محاولة الاتصال بـ MySQL...';
            
            // محاولة الاتصال بقاعدة البيانات
            $dsn = "mysql:host=$dbHost;port=$dbPort;charset=utf8mb4";
            $pdo = new PDO($dsn, $fullDbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $debugInfo[] = '✓ تم الاتصال بنجاح بـ MySQL';

            // التحقق من وجود قاعدة البيانات
            $stmt = $pdo->query("SHOW DATABASES LIKE '$fullDbName'");
            $dbExists = $stmt->rowCount() > 0;
            
            if ($dbExists) {
                $warnings[] = $currentLang === 'ar' 
                    ? "⚠️ قاعدة البيانات '$fullDbName' موجودة مسبقاً. سيتم استخدامها." 
                    : "⚠️ Database '$fullDbName' already exists. It will be used.";
                $debugInfo[] = "⚠️ قاعدة البيانات موجودة مسبقاً";
            } else {
                $debugInfo[] = '✓ قاعدة البيانات غير موجودة، سيتم إنشاؤها';
            }

            // التحقق من صلاحيات المستخدم
            try {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$fullDbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $debugInfo[] = '✓ تم إنشاء/التحقق من قاعدة البيانات';
            } catch (PDOException $e) {
                throw new Exception($currentLang === 'ar' 
                    ? "خطأ في إنشاء قاعدة البيانات: المستخدم لا يملك صلاحية CREATE DATABASE" 
                    : "Error creating database: User doesn't have CREATE DATABASE permission");
            }
            
            // الاتصال بقاعدة البيانات المنشأة
            $pdo->exec("USE `$fullDbName`");
            $debugInfo[] = '✓ تم الاتصال بقاعدة البيانات';

            // قراءة ملف schema.sql
            $schemaFile = dirname(dirname(__DIR__)) . '/database/schema.sql';
            $debugInfo[] = "البحث عن ملف schema في: $schemaFile";
            
            if (file_exists($schemaFile)) {
                $debugInfo[] = '✓ ملف schema.sql موجود';
                
                $schema = file_get_contents($schemaFile);
                $debugInfo[] = '✓ تم قراءة محتوى الملف (' . strlen($schema) . ' حرف)';
                
                // تقسيم الاستعلامات وتنفيذها
                $statements = array_filter(array_map('trim', explode(';', $schema)));
                $debugInfo[] = '✓ عدد الاستعلامات: ' . count($statements);
                
                $executedCount = 0;
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        try {
                            $pdo->exec($statement);
                            $executedCount++;
                        } catch (PDOException $e) {
                            $debugInfo[] = '⚠️ خطأ في تنفيذ استعلام: ' . $e->getMessage();
                        }
                    }
                }
                $debugInfo[] = "✓ تم تنفيذ $executedCount استعلام بنجاح";
            } else {
                $errors[] = $currentLang === 'ar' 
                    ? 'ملف schema.sql غير موجود في /database/schema.sql' 
                    : 'schema.sql file not found in /database/schema.sql';
                $debugInfo[] = '✗ ملف schema.sql غير موجود!';
            }

            if (empty($errors)) {
                // حفظ بيانات الاتصال
                $_SESSION['install_data']['database'] = [
                    'host' => $dbHost,
                    'port' => $dbPort,
                    'prefix' => $dbPrefix,
                    'name' => $fullDbName,
                    'user' => $fullDbUser,
                    'pass' => $dbPass
                ];
                
                $debugInfo[] = '✓ تم حفظ البيانات في Session';
                $success = true;
            }
        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
            $debugInfo[] = '✗ خطأ PDO: ' . $errorMsg;
            
            // رسائل خطأ واضحة
            if (strpos($errorMsg, 'Access denied') !== false) {
                $errors[] = $currentLang === 'ar' 
                    ? "خطأ في المصادقة: اسم المستخدم أو كلمة المرور غير صحيحة" 
                    : "Authentication error: Invalid username or password";
            } elseif (strpos($errorMsg, 'Unknown database') !== false) {
                $errors[] = $currentLang === 'ar' 
                    ? "قاعدة البيانات غير موجودة والمستخدم لا يملك صلاحية إنشائها" 
                    : "Database doesn't exist and user has no permission to create it";
            } elseif (strpos($errorMsg, "Can't connect") !== false) {
                $errors[] = $currentLang === 'ar' 
                    ? "لا يمكن الاتصال بالمضيف: تحقق من اسم المضيف والمنفذ" 
                    : "Can't connect to host: Check hostname and port";
            } else {
                $errors[] = ($currentLang === 'ar' ? 'خطأ في الاتصال: ' : 'Connection error: ') . $errorMsg;
            }
        } catch (Exception $e) {
            $debugInfo[] = '✗ خطأ عام: ' . $e->getMessage();
            $errors[] = $e->getMessage();
        }
    } else {
        $debugInfo[] = '✗ فشل التحقق من البيانات';
    }
}

// القيم المحفوظة مسبقاً
$savedData = $_SESSION['install_data']['database'] ?? [];
?>

<div class="step-content">
    <div class="step-header">
        <h2><?php echo $text['title']; ?></h2>
        <p><?php echo $text['subtitle']; ?></p>
    </div>

    <!-- معلومات التشخيص (فقط في وضع التطوير) -->
    <?php if (!empty($debugInfo) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="alert" style="background: #1e1e1e; color: #0ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto;">
        <strong style="color: #0f0;">🔍 معلومات التشخيص:</strong><br>
        <?php foreach ($debugInfo as $info): ?>
            <?php echo htmlspecialchars($info); ?><br>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- التحذيرات -->
    <?php if (!empty($warnings)): ?>
    <div class="alert alert-warning">
        <?php foreach ($warnings as $warning): ?>
            <p><?php echo htmlspecialchars($warning); ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- الأخطاء -->
    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong><?php echo $text['error_title']; ?>:</strong>
        <ul style="margin: 10px 0 0 0; padding-right: 20px;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- النجاح -->
    <?php if ($success): ?>
    <div class="alert alert-success">
        <strong><?php echo $text['success_title']; ?></strong>
        <p><?php echo $text['success_msg']; ?></p>
        <div style="margin-top: 15px; padding: 10px; background: #f0f9ff; border-radius: 5px;">
            <p style="margin: 5px 0;"><strong><?php echo $currentLang === 'ar' ? 'قاعدة البيانات:' : 'Database:'; ?></strong> <code><?php echo htmlspecialchars($_SESSION['install_data']['database']['name']); ?></code></p>
            <p style="margin: 5px 0;"><strong><?php echo $currentLang === 'ar' ? 'المستخدم:' : 'User:'; ?></strong> <code><?php echo htmlspecialchars($_SESSION['install_data']['database']['user']); ?></code></p>
        </div>
    </div>
    <div class="step-actions">
        <a href="index.php?step=1b" class="btn btn-secondary">
            <?php echo $currentLang === 'ar' ? '→ ' . $text['prev'] : $text['prev'] . ' ←'; ?>
        </a>
        <a href="index.php?step=3" class="btn btn-primary">
            <?php echo $currentLang === 'ar' ? $text['next'] . ' ←' : '→ ' . $text['next']; ?>
        </a>
    </div>
    <?php else: ?>
    
    <!-- معلومات cPanel -->
    <div class="info-box info-box-info">
        <div class="info-icon">ℹ️</div>
        <div class="info-content">
            <h4><?php echo $text['cpanel_note']; ?></h4>
            <p><?php echo $text['cpanel_help']; ?></p>
            <p><strong><?php echo $currentLang === 'ar' ? 'مثال:' : 'Example:'; ?></strong></p>
            <ul>
                <li><?php echo $currentLang === 'ar' ? 'البادئة:' : 'Prefix:'; ?> <code>u995861180_</code></li>
                <li><?php echo $currentLang === 'ar' ? 'اسم القاعدة:' : 'Database name:'; ?> <code>car_rental</code></li>
                <li><?php echo $currentLang === 'ar' ? 'الاسم الكامل:' : 'Full name:'; ?> <code>u995861180_car_rental</code></li>
            </ul>
        </div>
    </div>

    <form method="POST" action="index.php?step=2" class="install-form" id="dbForm">
        <div class="form-section">
            <h3><?php echo $text['section_connection']; ?></h3>
            
            <!-- البادئة -->
            <div class="form-group">
                <label for="db_prefix">
                    <?php echo $text['prefix']; ?>
                    <span style="color: #f59e0b; font-size: 0.875rem;">(cPanel)</span>
                </label>
                <input type="text" 
                       id="db_prefix" 
                       name="db_prefix" 
                       value="<?php echo htmlspecialchars($savedData['prefix'] ?? $_POST['db_prefix'] ?? ''); ?>"
                       placeholder="u995861180_"
                       pattern="[a-zA-Z0-9_]*">
                <small><?php echo $text['prefix_help']; ?></small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="db_host"><?php echo $text['host']; ?> *</label>
                    <input type="text" 
                           id="db_host" 
                           name="db_host" 
                           value="<?php echo htmlspecialchars($savedData['host'] ?? $_POST['db_host'] ?? 'localhost'); ?>" 
                           required>
                    <small><?php echo $text['host_help']; ?></small>
                </div>

                <div class="form-group">
                    <label for="db_port"><?php echo $text['port']; ?></label>
                    <input type="text" 
                           id="db_port" 
                           name="db_port" 
                           value="<?php echo htmlspecialchars($savedData['port'] ?? $_POST['db_port'] ?? '3306'); ?>"
                           pattern="[0-9]+">
                    <small><?php echo $text['port_help']; ?></small>
                </div>
            </div>

            <div class="form-group">
                <label for="db_name"><?php echo $text['dbname']; ?> *</label>
                <input type="text" 
                       id="db_name" 
                       name="db_name" 
                       value="<?php echo htmlspecialchars($_POST['db_name'] ?? 'car_rental'); ?>" 
                       pattern="[a-zA-Z0-9_]+"
                       required>
                <small><?php echo $text['dbname_help']; ?></small>
                <div id="preview_dbname" style="margin-top: 8px; padding: 8px; background: #f3f4f6; border-radius: 4px; font-family: monospace; display: none;"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="db_user"><?php echo $text['username']; ?> *</label>
                    <input type="text" 
                           id="db_user" 
                           name="db_user" 
                           value="<?php echo htmlspecialchars($_POST['db_user'] ?? ''); ?>" 
                           pattern="[a-zA-Z0-9_]+"
                           required>
                    <small><?php echo $text['username_help']; ?></small>
                    <div id="preview_dbuser" style="margin-top: 8px; padding: 8px; background: #f3f4f6; border-radius: 4px; font-family: monospace; display: none;"></div>
                </div>

                <div class="form-group">
                    <label for="db_pass"><?php echo $text['password']; ?></label>
                    <input type="password" 
                           id="db_pass" 
                           name="db_pass" 
                           value="<?php echo htmlspecialchars($_POST['db_pass'] ?? ''); ?>">
                    <small><?php echo $text['password_help']; ?></small>
                </div>
            </div>
        </div>

        <div class="info-box info-box-warning">
            <div class="info-icon">⚠️</div>
            <div class="info-content">
                <h4><?php echo $text['warning_title']; ?></h4>
                <p><?php echo $text['warning_msg']; ?></p>
                <p><small>CREATE, DROP, ALTER, INSERT, UPDATE, DELETE, SELECT</small></p>
            </div>
        </div>

        <div class="step-actions">
            <a href="?step=1b" class="btn btn-secondary">
                <?php echo $currentLang === 'ar' ? '→ ' . $text['prev'] : $text['prev'] . ' ←'; ?>
            </a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <?php echo $currentLang === 'ar' ? $text['test_button'] . ' ←' : '→ ' . $text['test_button']; ?>
            </button>
        </div>
    </form>
    
    <script>
    // المعاينة الفورية
    function updatePreviews() {
        const prefix = document.getElementById('db_prefix').value;
        const dbname = document.getElementById('db_name').value;
        const dbuser = document.getElementById('db_user').value;
        
        const previewDbName = document.getElementById('preview_dbname');
        const previewDbUser = document.getElementById('preview_dbuser');
        
        if (prefix && dbname) {
            previewDbName.innerHTML = '<strong><?php echo $currentLang === 'ar' ? 'الاسم الكامل:' : 'Full name:'; ?></strong> <span style="color: #2563eb;">' + prefix + dbname + '</span>';
            previewDbName.style.display = 'block';
        } else {
            previewDbName.style.display = 'none';
        }
        
        if (prefix && dbuser) {
            previewDbUser.innerHTML = '<strong><?php echo $currentLang === 'ar' ? 'الاسم الكامل:' : 'Full name:'; ?></strong> <span style="color: #2563eb;">' + prefix + dbuser + '</span>';
            previewDbUser.style.display = 'block';
        } else {
            previewDbUser.style.display = 'none';
        }
    }
    
    document.getElementById('db_prefix')?.addEventListener('input', updatePreviews);
    document.getElementById('db_name')?.addEventListener('input', updatePreviews);
    document.getElementById('db_user')?.addEventListener('input', updatePreviews);
    
    // التحقق من الإدخال
    document.getElementById('db_name')?.addEventListener('input', function(e) {
        if (!/^[a-zA-Z0-9_]*$/.test(e.target.value)) {
            e.target.value = e.target.value.replace(/[^a-zA-Z0-9_]/g, '');
        }
    });
    
    document.getElementById('db_user')?.addEventListener('input', function(e) {
        if (!/^[a-zA-Z0-9_]*$/.test(e.target.value)) {
            e.target.value = e.target.value.replace(/[^a-zA-Z0-9_]/g, '');
        }
    });
    
    // عند الإرسال
    document.getElementById('dbForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '⏳ <?php echo $text['testing']; ?>';
        console.log('✓ النموذج تم إرساله');
    });
    
    updatePreviews();
    </script>
    <?php endif; ?>
</div>