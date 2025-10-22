<?php
/**
 * File: step1b.php
 * Path: /install/steps/step1b.php
 * Purpose: Automatically create project folder structure
 * Dependencies: None
 */

$texts = [
    'ar' => [
        'title' => 'إنشاء هيكل المجلدات',
        'subtitle' => 'جاري إنشاء جميع المجلدات المطلوبة تلقائياً',
        'processing' => 'جاري المعالجة...',
        'created' => 'تم الإنشاء',
        'exists' => 'موجود مسبقاً',
        'failed' => 'فشل',
        'success_title' => 'تم بنجاح!',
        'success_msg' => 'تم إنشاء جميع المجلدات والملفات بنجاح',
        'error_title' => 'تحذير',
        'error_msg' => 'بعض المجلدات لم يتم إنشاؤها. يمكنك المتابعة أو إنشاؤها يدوياً.',
        'stats_created' => 'مجلدات تم إنشاؤها',
        'stats_exists' => 'مجلدات موجودة',
        'stats_protected' => 'ملفات حماية',
        'stats_errors' => 'أخطاء',
        'next' => 'التالي: قاعدة البيانات',
        'prev' => 'السابق',
        'retry' => 'إعادة المحاولة',
        'skip_note' => 'ملاحظة: يمكنك المتابعة حتى لو كانت هناك أخطاء. الخطوة التالية ستفحص الأذونات.'
    ],
    'en' => [
        'title' => 'Create Folder Structure',
        'subtitle' => 'Automatically creating all required folders',
        'processing' => 'Processing...',
        'created' => 'Created',
        'exists' => 'Already exists',
        'failed' => 'Failed',
        'success_title' => 'Success!',
        'success_msg' => 'All folders and files created successfully',
        'error_title' => 'Warning',
        'error_msg' => 'Some folders were not created. You can continue or create them manually.',
        'stats_created' => 'Folders Created',
        'stats_exists' => 'Existing Folders',
        'stats_protected' => 'Protection Files',
        'stats_errors' => 'Errors',
        'next' => 'Next: Database',
        'prev' => 'Previous',
        'retry' => 'Retry',
        'skip_note' => 'Note: You can continue even if there are errors. The next step will check permissions.'
    ]
];

$currentLang = $lang ?? 'ar';
$text = $texts[$currentLang];

// تهيئة install_data إذا لم تكن موجودة
if (!isset($_SESSION['install_data'])) {
    $_SESSION['install_data'] = [];
}

// حفظ حالة الخطوة 1b
$_SESSION['install_data']['step1b_completed'] = true;

// إحصائيات
$stats = [
    'created' => 0,
    'exists' => 0,
    'protected' => 0,
    'errors' => 0
];

$logs = [];

// دالة لإضافة سجل
function add_log($message, $type = 'info') {
    global $logs;
    $logs[] = ['message' => $message, 'type' => $type];
}

// هيكل المجلدات الكامل
$structure = [
    'config' => 'التكوين',
    'core' => 'النواة',
    'app' => 'التطبيق',
    'app/controllers' => 'المتحكمات',
    'app/controllers/frontend' => 'متحكمات الواجهة الأمامية',
    'app/controllers/backend' => 'متحكمات لوحة التحكم',
    'app/controllers/api' => 'متحكمات API',
    'app/models' => 'النماذج',
    'app/views' => 'العروض',
    'app/views/frontend' => 'عروض الواجهة الأمامية',
    'app/views/frontend/layouts' => 'قوالب الواجهة',
    'app/views/frontend/home' => 'الصفحة الرئيسية',
    'app/views/frontend/cars' => 'السيارات',
    'app/views/frontend/rental' => 'الحجوزات',
    'app/views/frontend/auth' => 'التسجيل',
    'app/views/frontend/profile' => 'الملف الشخصي',
    'app/views/frontend/pages' => 'الصفحات',
    'app/views/frontend/components' => 'المكونات',
    'app/views/backend' => 'عروض لوحة التحكم',
    'app/views/backend/layouts' => 'قوالب لوحة التحكم',
    'app/views/backend/dashboard' => 'لوحة القيادة',
    'app/views/backend/cars' => 'إدارة السيارات',
    'app/views/backend/rentals' => 'إدارة الحجوزات',
    'app/views/backend/customers' => 'إدارة العملاء',
    'app/views/backend/users' => 'إدارة المستخدمين',
    'app/views/backend/branches' => 'الفروع',
    'app/views/backend/maintenance' => 'الصيانة',
    'app/views/backend/violations' => 'المخالفات',
    'app/views/backend/finance' => 'المالية',
    'app/views/backend/reports' => 'التقارير',
    'app/views/backend/settings' => 'الإعدادات',
    'app/views/backend/languages' => 'اللغات',
    'app/views/backend/notifications' => 'الإشعارات',
    'app/views/backend/reviews' => 'التقييمات',
    'app/views/backend/pages' => 'الصفحات',
    'public' => 'العام',
    'public/assets' => 'الأصول',
    'public/assets/css' => 'CSS',
    'public/assets/css/frontend' => 'CSS الواجهة',
    'public/assets/css/backend' => 'CSS لوحة التحكم',
    'public/assets/css/common' => 'CSS المشترك',
    'public/assets/js' => 'JavaScript',
    'public/assets/js/frontend' => 'JS الواجهة',
    'public/assets/js/backend' => 'JS لوحة التحكم',
    'public/assets/js/common' => 'JS المشترك',
    'public/assets/images' => 'الصور',
    'public/assets/fonts' => 'الخطوط',
    'public/assets/vendor' => 'المكتبات',
    'public/uploads' => 'الملفات المرفوعة',
    'public/uploads/cars' => 'صور السيارات',
    'public/uploads/customers' => 'وثائق العملاء',
    'public/uploads/payments' => 'إيصالات الدفع',
    'public/uploads/documents' => 'المستندات',
    'public/uploads/users' => 'صور المستخدمين',
    'public/uploads/receipts' => 'الإيصالات',
    'public/uploads/contracts' => 'العقود',
    'public/uploads/violations' => 'المخالفات',
    'public/uploads/maintenance' => 'الصيانة',
    'database' => 'قاعدة البيانات',
    'database/seeds' => 'البيانات الأولية',
    'storage' => 'التخزين',
    'storage/logs' => 'السجلات',
    'storage/cache' => 'الذاكرة المؤقتة',
    'storage/cache/translations' => 'الترجمات',
    'storage/backups' => 'النسخ الاحتياطية',
    'storage/temp' => 'الملفات المؤقتة',
    'services' => 'الخدمات',
    'cron' => 'المهام المجدولة',
    'docs' => 'التوثيق'
];

// إنشاء المجلدات
$baseDir = dirname(dirname(__DIR__));

foreach ($structure as $folder => $description) {
    $fullPath = $baseDir . '/' . $folder;
    
    if (file_exists($fullPath)) {
        add_log("📁 $folder - " . $text['exists'], 'warning');
        $stats['exists']++;
    } else {
        if (@mkdir($fullPath, 0755, true)) {
            add_log("✅ $folder - " . $text['created'], 'success');
            $stats['created']++;
            @chmod($fullPath, 0755);
        } else {
            add_log("❌ $folder - " . $text['failed'], 'error');
            $stats['errors']++;
        }
    }
}

// إنشاء ملفات الحماية
$htaccess_deny = "# Deny all access\nOrder Deny,Allow\nDeny from all";
$htaccess_uploads = "# Allow safe files only
<FilesMatch \"\\.(jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx)$\">
    Order Allow,Deny
    Allow from all
</FilesMatch>

<FilesMatch \"\\.php$\">
    Order Deny,Allow
    Deny from all
</FilesMatch>

Options -Indexes";

$protected_dirs = [
    'storage/logs' => $htaccess_deny,
    'storage/cache' => $htaccess_deny,
    'storage/backups' => $htaccess_deny,
    'storage/temp' => $htaccess_deny,
    'public/uploads' => $htaccess_uploads
];

foreach ($protected_dirs as $dir => $content) {
    $fullPath = $baseDir . '/' . $dir;
    if (file_exists($fullPath)) {
        $htaccess_file = $fullPath . '/.htaccess';
        if (@file_put_contents($htaccess_file, $content)) {
            $stats['protected']++;
        }
    }
}

// إنشاء ملفات index.html
$index_html = "<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Access Denied</h1></body></html>";
foreach (array_keys($structure) as $folder) {
    $fullPath = $baseDir . '/' . $folder;
    if (file_exists($fullPath) && is_dir($fullPath)) {
        @file_put_contents($fullPath . '/index.html', $index_html);
    }
}

// إنشاء .gitignore
$gitignore = "# Environment
.env
.env.local

# Storage
storage/logs/*
!storage/logs/.gitkeep
storage/cache/*
!storage/cache/.gitkeep
storage/backups/*
!storage/backups/.gitkeep
storage/temp/*
!storage/temp/.gitkeep

# Uploads
public/uploads/*
!public/uploads/.gitkeep

# IDE
.idea/
.vscode/
*.sublime-*

# OS
.DS_Store
Thumbs.db

# Composer
/vendor/

# Install
install.lock";

@file_put_contents($baseDir . '/.gitignore', $gitignore);

// إنشاء .gitkeep
$gitkeep_dirs = ['storage/logs', 'storage/cache', 'storage/backups', 'storage/temp', 'public/uploads'];
foreach ($gitkeep_dirs as $dir) {
    $fullPath = $baseDir . '/' . $dir;
    if (file_exists($fullPath)) {
        @file_put_contents($fullPath . '/.gitkeep', '');
    }
}

$hasErrors = $stats['errors'] > 0;
?>

<div class="step-content">
    <div class="step-header">
        <h2><?php echo $text['title']; ?></h2>
        <p><?php echo $text['subtitle']; ?></p>
    </div>

    <!-- إحصائيات -->
    <div class="structure-stats">
        <div class="stat-card stat-success">
            <div class="stat-icon">✅</div>
            <div class="stat-number"><?php echo $stats['created']; ?></div>
            <div class="stat-label"><?php echo $text['stats_created']; ?></div>
        </div>
        
        <div class="stat-card stat-warning">
            <div class="stat-icon">📁</div>
            <div class="stat-number"><?php echo $stats['exists']; ?></div>
            <div class="stat-label"><?php echo $text['stats_exists']; ?></div>
        </div>
        
        <div class="stat-card stat-info">
            <div class="stat-icon">🔒</div>
            <div class="stat-number"><?php echo $stats['protected']; ?></div>
            <div class="stat-label"><?php echo $text['stats_protected']; ?></div>
        </div>
        
        <div class="stat-card <?php echo $hasErrors ? 'stat-error' : 'stat-success'; ?>">
            <div class="stat-icon"><?php echo $hasErrors ? '⚠️' : '✓'; ?></div>
            <div class="stat-number"><?php echo $stats['errors']; ?></div>
            <div class="stat-label"><?php echo $text['stats_errors']; ?></div>
        </div>
    </div>

    <!-- السجل -->
    <div class="structure-log">
        <h3><?php echo $currentLang === 'ar' ? '📋 سجل العمليات' : '📋 Operation Log'; ?></h3>
        <div class="log-container">
            <?php foreach ($logs as $log): ?>
                <div class="log-item log-<?php echo $log['type']; ?>">
                    <?php echo htmlspecialchars($log['message']); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- النتيجة -->
    <?php if (!$hasErrors): ?>
    <div class="alert alert-success">
        <strong><?php echo $text['success_title']; ?></strong>
        <p><?php echo $text['success_msg']; ?></p>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">
        <strong><?php echo $text['error_title']; ?></strong>
        <p><?php echo $text['error_msg']; ?></p>
        <p style="margin-top: 10px;"><small><?php echo $text['skip_note']; ?></small></p>
    </div>
    <?php endif; ?>

    <!-- الإجراءات -->
    <div class="step-actions">
        <a href="index.php?step=0" class="btn btn-secondary">
            <?php echo $currentLang === 'ar' ? '→ ' . $text['prev'] : $text['prev'] . ' ←'; ?>
        </a>
        
        <?php if ($hasErrors): ?>
        <a href="index.php?step=1b" class="btn btn-warning">
            🔄 <?php echo $text['retry']; ?>
        </a>
        <?php endif; ?>
        
        <a href="index.php?step=2" class="btn btn-primary" id="nextBtn">
            <?php echo $currentLang === 'ar' ? 'التالي: قاعدة البيانات ←' : '→ Next: Database'; ?>
        </a>
    </div>
</div>

<script>
// تتبع النقر على زر التالي
document.getElementById('nextBtn')?.addEventListener('click', function(e) {
    console.log('🔵 تم النقر على زر التالي');
    console.log('🔗 الرابط:', this.href);
    console.log('📍 الموقع الحالي:', window.location.href);
    
    // إضافة معلومات تشخيصية
    console.log('🔍 pathname:', window.location.pathname);
    console.log('🔍 search:', window.location.search);
});

// عرض معلومات في console
console.log('✅ step1b.php تم تحميله');
console.log('📊 إحصائيات:', {
    created: <?php echo $stats['created']; ?>,
    exists: <?php echo $stats['exists']; ?>,
    errors: <?php echo $stats['errors']; ?>
});
console.log('📍 المسار الكامل:', window.location.href);
</script>

<style>
.structure-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    border: 2px solid;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-success {
    border-color: #10b981;
    background: linear-gradient(135deg, #f0fdf4 0%, #d1fae5 100%);
}

.stat-warning {
    border-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.stat-info {
    border-color: #06b6d4;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
}

.stat-error {
    border-color: #ef4444;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

.stat-icon {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.stat-number {
    font-size: 3rem;
    font-weight: bold;
    color: var(--gray-800);
    margin: 0.5rem 0;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    font-weight: 500;
}

.structure-log {
    margin: 2rem 0;
}

.structure-log h3 {
    margin-bottom: 1rem;
    color: var(--gray-800);
}

.log-container {
    background: #1e1e1e;
    border-radius: 8px;
    padding: 1.5rem;
    max-height: 400px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
}

.log-item {
    padding: 0.5rem;
    margin: 0.25rem 0;
    border-radius: 4px;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.log-success {
    color: #10b981;
}

.log-warning {
    color: #f59e0b;
}

.log-error {
    color: #ef4444;
}

.log-info {
    color: #06b6d4;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
}

@media (max-width: 768px) {
    .structure-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1.5rem 1rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
}
</style>