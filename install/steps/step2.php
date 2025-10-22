<?php
/**
 * File: step2.php
 * Path: /install/steps/step2.php
 * Purpose: Database configuration and setup
 * Dependencies: PDO, MySQL
 */

$texts = [
    'ar' => [
        'title' => 'إعداد قاعدة البيانات',
        'subtitle' => 'قم بإدخال معلومات الاتصال بقاعدة البيانات',
        'section_connection' => 'معلومات الاتصال',
        'host' => 'اسم المضيف',
        'host_help' => 'عادة ما يكون localhost',
        'port' => 'المنفذ',
        'port_help' => 'المنفذ الافتراضي 3306',
        'dbname' => 'اسم قاعدة البيانات',
        'dbname_help' => 'سيتم إنشاؤها إذا لم تكن موجودة',
        'username' => 'اسم المستخدم',
        'password' => 'كلمة المرور',
        'password_help' => 'اتركها فارغة إذا لم تكن محددة',
        'test_button' => 'اختبار الاتصال والمتابعة',
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
        'host' => 'Host',
        'host_help' => 'Usually localhost',
        'port' => 'Port',
        'port_help' => 'Default port is 3306',
        'dbname' => 'Database Name',
        'dbname_help' => 'Will be created if not exists',
        'username' => 'Username',
        'password' => 'Password',
        'password_help' => 'Leave empty if not set',
        'test_button' => 'Test Connection and Continue',
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
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim($_POST['db_host'] ?? '');
    $dbPort = trim($_POST['db_port'] ?? '3306');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? '';

    // التحقق من البيانات
    if (empty($dbHost)) $errors[] = $currentLang === 'ar' ? 'اسم المضيف مطلوب' : 'Host is required';
    if (empty($dbName)) $errors[] = $currentLang === 'ar' ? 'اسم قاعدة البيانات مطلوب' : 'Database name is required';
    if (empty($dbUser)) $errors[] = $currentLang === 'ar' ? 'اسم المستخدم مطلوب' : 'Username is required';

    if (empty($errors)) {
        try {
            // محاولة الاتصال بقاعدة البيانات
            $dsn = "mysql:host=$dbHost;port=$dbPort;charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // إنشاء قاعدة البيانات إذا لم تكن موجودة
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // الاتصال بقاعدة البيانات المنشأة
            $pdo->exec("USE `$dbName`");

            // قراءة ملف schema.sql
            $schemaFile = dirname(dirname(__DIR__)) . '/database/schema.sql';
            if (file_exists($schemaFile)) {
                $schema = file_get_contents($schemaFile);
                // تقسيم الاستعلامات وتنفيذها
                $statements = array_filter(array_map('trim', explode(';', $schema)));
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        $pdo->exec($statement);
                    }
                }
            } else {
                $errors[] = $currentLang === 'ar' 
                    ? 'ملف schema.sql غير موجود في /database/schema.sql' 
                    : 'schema.sql file not found in /database/schema.sql';
            }

            if (empty($errors)) {
                // حفظ بيانات الاتصال
                $_SESSION['install_data']['database'] = [
                    'host' => $dbHost,
                    'port' => $dbPort,
                    'name' => $dbName,
                    'user' => $dbUser,
                    'pass' => $dbPass
                ];

                $success = true;
            }
        } catch (PDOException $e) {
            $errors[] = ($currentLang === 'ar' ? 'خطأ في الاتصال: ' : 'Connection error: ') . $e->getMessage();
        }
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

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong><?php echo $text['error_title']; ?>:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <strong><?php echo $text['success_title']; ?></strong>
        <p><?php echo $text['success_msg']; ?></p>
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
    <form method="POST" action="index.php?step=2" class="install-form">
        <div class="form-section">
            <h3><?php echo $text['section_connection']; ?></h3>
            
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
                           value="<?php echo htmlspecialchars($savedData['port'] ?? $_POST['db_port'] ?? '3306'); ?>">
                    <small><?php echo $text['port_help']; ?></small>
                </div>
            </div>

            <div class="form-group">
                <label for="db_name"><?php echo $text['dbname']; ?> *</label>
                <input type="text" 
                       id="db_name" 
                       name="db_name" 
                       value="<?php echo htmlspecialchars($savedData['name'] ?? $_POST['db_name'] ?? 'car_rental_db'); ?>" 
                       required>
                <small><?php echo $text['dbname_help']; ?></small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="db_user"><?php echo $text['username']; ?> *</label>
                    <input type="text" 
                           id="db_user" 
                           name="db_user" 
                           value="<?php echo htmlspecialchars($savedData['user'] ?? $_POST['db_user'] ?? 'root'); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="db_pass"><?php echo $text['password']; ?></label>
                    <input type="password" 
                           id="db_pass" 
                           name="db_pass" 
                           value="<?php echo htmlspecialchars($savedData['pass'] ?? $_POST['db_pass'] ?? ''); ?>">
                    <small><?php echo $text['password_help']; ?></small>
                </div>
            </div>
        </div>

        <div class="info-box info-box-warning">
            <div class="info-icon">⚠️</div>
            <div class="info-content">
                <h4><?php echo $text['warning_title']; ?></h4>
                <p><?php echo $text['warning_msg']; ?> (CREATE, DROP, ALTER, INSERT, UPDATE, DELETE)</p>
            </div>
        </div>

        <div class="step-actions">
            <a href="index.php?step=1b" class="btn btn-secondary">
                <?php echo $currentLang === 'ar' ? '→ ' . $text['prev'] : $text['prev'] . ' ←'; ?>
            </a>
            <button type="submit" class="btn btn-primary">
                <?php echo $currentLang === 'ar' ? $text['test_button'] . ' ←' : '→ ' . $text['test_button']; ?>
            </button>
        </div>
    </form>
    <?php endif; ?>
</div>