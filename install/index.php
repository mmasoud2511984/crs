<?php
/**
 * File: index.php
 * Path: /install/index.php
 * Purpose: Main installation wizard entry point
 * Dependencies: PHP 8.2+, MySQL 8.0+
 */

// التحقق من وجود ملف القفل (التثبيت مكتمل)
if (file_exists(__DIR__ . '/install.lock')) {
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>التثبيت مكتمل</title>
        <link rel="stylesheet" href="assets/css/install.css">
    </head>
    <body>
        <div class="install-container">
            <div class="install-header">
                <div class="logo">
                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none">
                        <rect width="50" height="50" rx="10" fill="#10b981"/>
                        <path d="M15 25L22 32L35 18" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <h1>التثبيت مكتمل بالفعل!</h1>
            </div>
            <div class="install-content">
                <div class="alert alert-success">
                    <strong>نجح!</strong> تم تثبيت النظام بنجاح سابقاً.
                </div>
                <div class="info-box info-box-warning">
                    <div class="info-icon">⚠️</div>
                    <div class="info-content">
                        <h4>تنبيه أمني</h4>
                        <p>لأسباب أمنية، يُرجى حذف مجلد <code>/install/</code> بالكامل من السيرفر.</p>
                    </div>
                </div>
                <div class="final-actions" style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                    <a href="../" class="btn btn-primary">الذهاب إلى الموقع</a>
                    <a href="../admin/login" class="btn btn-secondary">تسجيل الدخول</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

session_start();

// اختيار اللغة الافتراضية للمثبت (يمكن تغييرها من خطوة 0)
$installerLang = $_SESSION['installer_lang'] ?? 'ar';
$isRTL = $installerLang === 'ar';

// تغيير لغة المثبت
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
    $_SESSION['installer_lang'] = $_GET['lang'];
    $installerLang = $_GET['lang'];
    $isRTL = $installerLang === 'ar';
    // إعادة التوجيه بدون معامل lang
    $currentStep = $_GET['step'] ?? 0;
    header('Location: ?step=' . $currentStep);
    exit;
}

// نصوص الترجمة
$translations = [
    'ar' => [
        'title' => 'نظام إدارة تأجير السيارات',
        'subtitle' => 'معالج التثبيت السريع والسهل',
        'copyright' => '© 2025 نظام إدارة تأجير السيارات - جميع الحقوق محفوظة',
        'steps' => [
            0 => ['icon' => '🌐', 'title' => 'اختيار اللغة'],
            '1b' => ['icon' => '📂', 'title' => 'إنشاء المجلدات'],
            1 => ['icon' => '📋', 'title' => 'فحص المتطلبات'],
            2 => ['icon' => '🗄️', 'title' => 'قاعدة البيانات'],
            3 => ['icon' => '🏢', 'title' => 'معلومات الشركة'],
            4 => ['icon' => '👤', 'title' => 'حساب المدير'],
            5 => ['icon' => '🌍', 'title' => 'إعدادات اللغة'],
            6 => ['icon' => '✅', 'title' => 'الإنهاء']
        ]
    ],
    'en' => [
        'title' => 'Car Rental Management System',
        'subtitle' => 'Quick and Easy Installation Wizard',
        'copyright' => '© 2025 Car Rental Management System - All Rights Reserved',
        'steps' => [
            0 => ['icon' => '🌐', 'title' => 'Language'],
            '1b' => ['icon' => '📂', 'title' => 'Create Folders'],
            1 => ['icon' => '📋', 'title' => 'Requirements'],
            2 => ['icon' => '🗄️', 'title' => 'Database'],
            3 => ['icon' => '🏢', 'title' => 'Company Info'],
            4 => ['icon' => '👤', 'title' => 'Admin Account'],
            5 => ['icon' => '🌍', 'title' => 'Languages'],
            6 => ['icon' => '✅', 'title' => 'Finish']
        ]
    ]
];

$t = $translations[$installerLang];

// تحديد الخطوة الحالية
$step = isset($_GET['step']) ? $_GET['step'] : 0;

// قائمة الخطوات المسموح بها
$allowedSteps = ['0', '1b', '1', '2', '3', '4', '5', '6'];

// التحقق من صحة الخطوة
if (!in_array($step, $allowedSteps, true)) {
    $step = 0;
}

// التحقق من اختيار اللغة
// فقط إذا كان في خطوة بعد 0 ولم يتم اختيار اللغة
if ($step !== '0' && !isset($_SESSION['installer_lang'])) {
    // حفظ اللغة الافتراضية
    $_SESSION['installer_lang'] = 'ar';
    $installerLang = 'ar';
}

// تخزين البيانات في الجلسة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['install_data'])) {
        $_SESSION['install_data'] = [];
    }
    foreach ($_POST as $key => $value) {
        if ($key !== 'csrf_token') {
            $_SESSION['install_data'][$key] = $value;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="<?php echo $installerLang; ?>" dir="<?php echo $isRTL ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    <link rel="stylesheet" href="assets/css/install.css">
</head>
<body>
    <div class="install-container">
        <!-- Language Switcher -->
        <div class="language-switcher">
            <a href="?step=<?php echo $step; ?>&lang=ar" class="<?php echo $installerLang === 'ar' ? 'active' : ''; ?>">
                🇸🇦 العربية
            </a>
            <a href="?step=<?php echo $step; ?>&lang=en" class="<?php echo $installerLang === 'en' ? 'active' : ''; ?>">
                🇬🇧 English
            </a>
        </div>

        <!-- Header -->
        <div class="install-header">
            <div class="logo">
                <svg width="50" height="50" viewBox="0 0 50 50" fill="none">
                    <rect width="50" height="50" rx="10" fill="#2563eb"/>
                    <path d="M15 25L22 32L35 18" stroke="white" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>
            <h1><?php echo $t['title']; ?></h1>
            <p class="subtitle"><?php echo $t['subtitle']; ?></p>
        </div>

        <!-- Progress Steps -->
        <div class="progress-steps">
            <?php
            foreach ($t['steps'] as $num => $info) {
                // تحديد الخطوة النشطة
                $active = ($num === $step || (string)$num === (string)$step) ? 'active' : '';
                
                // تحديد الخطوات المكتملة
                $completed = '';
                if ($step === '1b' && $num === 0) {
                    $completed = 'completed';
                } elseif (is_numeric($step) && is_numeric($num) && $num < $step) {
                    $completed = 'completed';
                } elseif ($step === '1b' && $num === '1b') {
                    // الخطوة الحالية
                } elseif (is_numeric($step) && $num === '1b' && $step >= 1) {
                    $completed = 'completed';
                }
                
                echo '<div class="step-item ' . $active . ' ' . $completed . '">';
                echo '<div class="step-number">' . $info['icon'] . '</div>';
                echo '<div class="step-title">' . $info['title'] . '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Content Area -->
        <div class="install-content">
            <?php
            // تحميل الخطوة المطلوبة
            $stepFile = __DIR__ . '/steps/step' . $step . '.php';
            
            // تمرير متغيرات الترجمة للخطوات
            $lang = $installerLang;
            $dir = $isRTL ? 'rtl' : 'ltr';
            
            if (file_exists($stepFile)) {
                require_once $stepFile;
            } else {
                // عرض رسالة خطأ إذا كانت الخطوة غير موجودة
                echo '<div class="alert alert-error">';
                echo '<strong>' . ($installerLang === 'ar' ? 'خطأ:' : 'Error:') . '</strong> ';
                echo $installerLang === 'ar' 
                    ? 'الخطوة ' . $step . ' غير موجودة. يرجى إنشاء ملف steps/step' . $step . '.php' 
                    : 'Step ' . $step . ' not found. Please create file steps/step' . $step . '.php';
                echo '</div>';
                
                echo '<div class="step-actions">';
                if ($step > 0) {
                    echo '<a href="?step=' . ($step - 1) . '" class="btn btn-secondary">';
                    echo $installerLang === 'ar' ? '→ السابق' : '← Previous';
                    echo '</a>';
                }
                echo '<a href="?step=0" class="btn btn-primary">';
                echo $installerLang === 'ar' ? 'العودة للبداية' : 'Back to Start';
                echo '</a>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Footer -->
        <div class="install-footer">
            <p><?php echo $t['copyright']; ?></p>
        </div>
    </div>

    <script>
        // تمرير لغة المثبت لـ JavaScript
        window.installerLang = '<?php echo $installerLang; ?>';
    </script>
    <script src="assets/js/install.js"></script>
</body>
</html>