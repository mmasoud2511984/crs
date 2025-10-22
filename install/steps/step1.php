<?php
/**
 * File: step1-requirements.php
 * Path: /install/steps/step1-requirements.php
 * Purpose: Check system requirements before installation
 * Dependencies: None
 */

// فحص متطلبات النظام
$requirements = [
    'php_version' => [
        'title' => 'إصدار PHP (8.2 أو أحدث)',
        'required' => '8.2.0',
        'current' => PHP_VERSION,
        'status' => version_compare(PHP_VERSION, '8.2.0', '>=')
    ],
    'pdo' => [
        'title' => 'PDO Extension',
        'required' => 'مطلوب',
        'current' => extension_loaded('pdo') ? 'متوفر' : 'غير متوفر',
        'status' => extension_loaded('pdo')
    ],
    'pdo_mysql' => [
        'title' => 'PDO MySQL Extension',
        'required' => 'مطلوب',
        'current' => extension_loaded('pdo_mysql') ? 'متوفر' : 'غير متوفر',
        'status' => extension_loaded('pdo_mysql')
    ],
    'mbstring' => [
        'title' => 'Mbstring Extension',
        'required' => 'مطلوب',
        'current' => extension_loaded('mbstring') ? 'متوفر' : 'غير متوفر',
        'status' => extension_loaded('mbstring')
    ],
    'json' => [
        'title' => 'JSON Extension',
        'required' => 'مطلوب',
        'current' => extension_loaded('json') ? 'متوفر' : 'غير متوفر',
        'status' => extension_loaded('json')
    ],
    'fileinfo' => [
        'title' => 'Fileinfo Extension',
        'required' => 'مطلوب',
        'current' => extension_loaded('fileinfo') ? 'متوفر' : 'غير متوفر',
        'status' => extension_loaded('fileinfo')
    ],
    'gd' => [
        'title' => 'GD Extension',
        'required' => 'مطلوب',
        'current' => extension_loaded('gd') ? 'متوفر' : 'غير متوفر',
        'status' => extension_loaded('gd')
    ]
];

// فحص أذونات المجلدات
$permissions = [
    '../storage/logs/' => [
        'title' => 'مجلد السجلات',
        'path' => '../storage/logs/',
        'writable' => is_writable('../storage/logs/')
    ],
    '../storage/cache/' => [
        'title' => 'مجلد الكاش',
        'path' => '../storage/cache/',
        'writable' => is_writable('../storage/cache/')
    ],
    '../storage/backups/' => [
        'title' => 'مجلد النسخ الاحتياطية',
        'path' => '../storage/backups/',
        'writable' => is_writable('../storage/backups/')
    ],
    '../public/uploads/' => [
        'title' => 'مجلد الملفات المرفوعة',
        'path' => '../public/uploads/',
        'writable' => is_writable('../public/uploads/')
    ]
];

// التحقق من نجاح جميع المتطلبات
$allRequirementsMet = true;
foreach ($requirements as $req) {
    if (!$req['status']) {
        $allRequirementsMet = false;
        break;
    }
}

foreach ($permissions as $perm) {
    if (!$perm['writable']) {
        $allRequirementsMet = false;
        break;
    }
}
?>

<div class="step-content">
    <div class="step-header">
        <h2>📋 الخطوة 1: فحص المتطلبات</h2>
        <p>يرجى التأكد من توفر جميع المتطلبات التالية قبل المتابعة</p>
    </div>

    <!-- متطلبات PHP -->
    <div class="requirements-section">
        <h3>متطلبات PHP والإضافات</h3>
        <table class="requirements-table">
            <thead>
                <tr>
                    <th>المتطلب</th>
                    <th>المطلوب</th>
                    <th>الحالي</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requirements as $req): ?>
                <tr class="<?php echo $req['status'] ? 'success' : 'error'; ?>">
                    <td><?php echo $req['title']; ?></td>
                    <td><?php echo $req['required']; ?></td>
                    <td><?php echo $req['current']; ?></td>
                    <td>
                        <?php if ($req['status']): ?>
                            <span class="badge badge-success">✓ متوفر</span>
                        <?php else: ?>
                            <span class="badge badge-error">✗ غير متوفر</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- أذونات المجلدات -->
    <div class="requirements-section">
        <h3>أذونات المجلدات (يجب أن تكون قابلة للكتابة)</h3>
        <table class="requirements-table">
            <thead>
                <tr>
                    <th>المجلد</th>
                    <th>المسار</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($permissions as $perm): ?>
                <tr class="<?php echo $perm['writable'] ? 'success' : 'error'; ?>">
                    <td><?php echo $perm['title']; ?></td>
                    <td><code><?php echo $perm['path']; ?></code></td>
                    <td>
                        <?php if ($perm['writable']): ?>
                            <span class="badge badge-success">✓ قابل للكتابة</span>
                        <?php else: ?>
                            <span class="badge badge-error">✗ غير قابل للكتابة</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (!$allRequirementsMet): ?>
        <div class="alert alert-warning">
            <strong>تنبيه:</strong> يرجى إصلاح المشاكل أعلاه قبل المتابعة. لتصحيح أذونات المجلدات، استخدم:
            <pre>chmod -R 777 storage/ public/uploads/</pre>
        </div>
        <?php endif; ?>
    </div>

    <!-- معلومات إضافية -->
    <div class="info-boxes">
        <div class="info-box">
            <div class="info-icon">💾</div>
            <div class="info-content">
                <h4>قاعدة البيانات</h4>
                <p>MySQL 8.0 أو MariaDB 10.5 أو أحدث</p>
            </div>
        </div>
        <div class="info-box">
            <div class="info-icon">📦</div>
            <div class="info-content">
                <h4>المساحة المطلوبة</h4>
                <p>100 ميجابايت على الأقل</p>
            </div>
        </div>
        <div class="info-box">
            <div class="info-icon">⏱️</div>
            <div class="info-content">
                <h4>وقت التثبيت</h4>
                <p>تقريباً 5-10 دقائق</p>
            </div>
        </div>
    </div>

    <!-- أزرار التنقل -->
    <div class="step-actions">
        <a href="?step=1b" class="btn btn-secondary">
            → السابق
        </a>
        <button type="button" class="btn btn-secondary" onclick="location.reload()">
            🔄 إعادة الفحص
        </button>
        <?php if ($allRequirementsMet): ?>
        <a href="?step=2" class="btn btn-primary">
            التالي: إعداد قاعدة البيانات ←
        </a>
        <?php else: ?>
        <button type="button" class="btn btn-primary" disabled>
            يجب حل المشاكل أولاً
        </button>
        <?php endif; ?>
    </div>
</div>