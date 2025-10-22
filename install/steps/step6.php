<?php
/**
 * File: step6-finish.php
 * Path: /install/steps/step6-finish.php
 * Purpose: Complete installation and insert all data
 * Dependencies: All previous steps data
 */

$errors = [];
$success = false;
$installed = false;

// التحقق من وجود جميع البيانات المطلوبة
if (!isset($_SESSION['install_data']['database']) ||
    !isset($_SESSION['install_data']['company']) ||
    !isset($_SESSION['install_data']['admin']) ||
    !isset($_SESSION['install_data']['languages'])) {
    $errors[] = 'بيانات غير كاملة. يرجى إكمال جميع الخطوات السابقة.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    try {
        $dbData = $_SESSION['install_data']['database'];
        $companyData = $_SESSION['install_data']['company'];
        $adminData = $_SESSION['install_data']['admin'];
        $langData = $_SESSION['install_data']['languages'];

        // الاتصال بقاعدة البيانات
        $dsn = "mysql:host={$dbData['host']};port={$dbData['port']};dbname={$dbData['name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbData['user'], $dbData['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // بدء المعاملة
        $pdo->beginTransaction();

        // 1. إدخال اللغات
        $stmt = $pdo->prepare("INSERT INTO languages (name, code, direction, is_default, is_active, created_at) 
                               VALUES (?, ?, ?, ?, ?, NOW())");
        
        $stmt->execute(['العربية', 'ar', 'rtl', $langData['default'] === 'ar' ? 1 : 0, 1]);
        $stmt->execute(['English', 'en', 'ltr', $langData['default'] === 'en' ? 1 : 0, 1]);

        // 2. إدخال الترجمات الأساسية (سيتم تحميلها من ملف SQL منفصل)
        $translationsFile = dirname(dirname(__DIR__)) . '/database/seeds/translations.sql';
        if (file_exists($translationsFile)) {
            $translations = file_get_contents($translationsFile);
            $pdo->exec($translations);
        }

        // 3. إنشاء الأدوار الأساسية
        $stmt = $pdo->prepare("INSERT INTO roles (name, slug, is_system, created_at) VALUES (?, ?, 1, NOW())");
        $stmt->execute(['مدير النظام', 'super_admin']);
        $superAdminRoleId = $pdo->lastInsertId();
        
        $stmt->execute(['مدير', 'admin']);
        $stmt->execute(['موظف', 'employee']);

        // 4. إدخال الصلاحيات الأساسية
        $permissions = [
            ['إدارة المستخدمين', 'manage_users', 'users'],
            ['إدارة السيارات', 'manage_cars', 'cars'],
            ['إدارة الحجوزات', 'manage_rentals', 'rentals'],
            ['إدارة العملاء', 'manage_customers', 'customers'],
            ['إدارة المالية', 'manage_finance', 'finance'],
            ['عرض التقارير', 'view_reports', 'reports'],
            ['إدارة الإعدادات', 'manage_settings', 'settings']
        ];

        $stmt = $pdo->prepare("INSERT INTO permissions (name, slug, category, created_at) VALUES (?, ?, ?, NOW())");
        $permissionIds = [];
        foreach ($permissions as $perm) {
            $stmt->execute($perm);
            $permissionIds[] = $pdo->lastInsertId();
        }

        // 5. ربط جميع الصلاحيات بدور المدير الأعلى
        $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        foreach ($permissionIds as $permId) {
            $stmt->execute([$superAdminRoleId, $permId]);
        }

        // 6. إنشاء الفرع الرئيسي
        $stmt = $pdo->prepare("INSERT INTO branches (name, code, address, city, country, phone, email, whatsapp, is_active, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([
            $companyData['name'],
            'MAIN',
            $companyData['address'] ?? '',
            $companyData['city'] ?? '',
            $companyData['country'],
            $companyData['phone'],
            $companyData['email'],
            $companyData['whatsapp'] ?? $companyData['phone']
        ]);
        $branchId = $pdo->lastInsertId();

        // 7. إنشاء حساب المدير
        $stmt = $pdo->prepare("INSERT INTO users (branch_id, username, email, password_hash, full_name, phone, is_active, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([
            $branchId,
            $adminData['username'],
            $adminData['email'],
            $adminData['password'],
            $adminData['full_name'],
            $adminData['phone'] ?? null
        ]);
        $userId = $pdo->lastInsertId();

        // 8. ربط المستخدم بدور المدير الأعلى
        $stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
        $stmt->execute([$userId, $superAdminRoleId]);

        // 9. إدخال الإعدادات
        $settings = [
            ['company_name_ar', $companyData['name'], 'text', 'company', 1],
            ['company_name_en', $companyData['name_en'], 'text', 'company', 1],
            ['company_email', $companyData['email'], 'text', 'company', 1],
            ['company_phone', $companyData['phone'], 'text', 'company', 1],
            ['company_whatsapp', $companyData['whatsapp'] ?? $companyData['phone'], 'text', 'company', 1],
            ['company_address', $companyData['address'] ?? '', 'text', 'company', 1],
            ['company_city', $companyData['city'] ?? '', 'text', 'company', 1],
            ['company_country', $companyData['country'], 'text', 'company', 1],
            ['default_currency', $companyData['currency'], 'text', 'general', 1],
            ['timezone', $companyData['timezone'], 'text', 'general', 1],
            ['default_language', $langData['default'], 'text', 'general', 1],
            ['date_format', 'Y-m-d', 'text', 'general', 1],
            ['time_format', 'H:i:s', 'text', 'general', 1]
        ];

        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, category, is_public, created_at) 
                               VALUES (?, ?, ?, ?, ?, NOW())");
        foreach ($settings as $setting) {
            $stmt->execute($setting);
        }

        // 10. إنشاء الصفحات الثابتة
        $pages = [
            ['about', 'about-us', 1, 1, 1],
            ['contact', 'contact-us', 1, 1, 2],
            ['terms', 'terms-conditions', 1, 1, 3],
            ['privacy', 'privacy-policy', 1, 1, 4]
        ];

        $stmt = $pdo->prepare("INSERT INTO pages (page_key, slug, is_active, display_in_footer, display_order, created_at) 
                               VALUES (?, ?, ?, ?, ?, NOW())");
        foreach ($pages as $page) {
            $stmt->execute($page);
        }

        // 11. إنشاء ملف .env
        $envContent = "# Database Configuration
DB_HOST={$dbData['host']}
DB_PORT={$dbData['port']}
DB_NAME={$dbData['name']}
DB_USER={$dbData['user']}
DB_PASS={$dbData['pass']}

# Application Configuration
APP_NAME=\"{$companyData['name']}\"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

# Security
APP_KEY=" . bin2hex(random_bytes(16)) . "
SESSION_LIFETIME=120

# Timezone & Locale
TIMEZONE={$companyData['timezone']}
DEFAULT_LANGUAGE={$langData['default']}
CURRENCY={$companyData['currency']}

# Installation
INSTALLED=true
INSTALL_DATE=" . date('Y-m-d H:i:s') . "
";

        file_put_contents(dirname(dirname(__DIR__)) . '/.env', $envContent);

        // تأكيد المعاملة
        $pdo->commit();

        // حذف مجلد التثبيت للأمان
        // ملاحظة: سيتم إضافة ملف install.lock بدلاً من حذف المجلد
        file_put_contents(dirname(__DIR__) . '/install.lock', date('Y-m-d H:i:s'));

        $success = true;
        $installed = true;

        // مسح بيانات الجلسة
        unset($_SESSION['install_data']);

    } catch (PDOException $e) {
        $pdo->rollBack();
        $errors[] = 'خطأ في قاعدة البيانات: ' . $e->getMessage();
    } catch (Exception $e) {
        if (isset($pdo)) $pdo->rollBack();
        $errors[] = 'خطأ: ' . $e->getMessage();
    }
}
?>

<div class="step-content">
    <div class="step-header">
        <h2>✅ الخطوة 6: إنهاء التثبيت</h2>
        <p>مراجعة البيانات وإتمام عملية التثبيت</p>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>خطأ:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="step-actions">
        <a href="?step=1" class="btn btn-secondary">→ العودة للبداية</a>
    </div>
    <?php elseif ($installed): ?>
    <div class="success-animation">
        <div class="success-icon">🎉</div>
        <h2>تم التثبيت بنجاح!</h2>
        <p>تم إعداد نظام إدارة تأجير السيارات بنجاح</p>
    </div>

    <div class="installation-summary">
        <h3>ملخص التثبيت</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-icon">✓</div>
                <div class="summary-content">
                    <h4>قاعدة البيانات</h4>
                    <p><?php echo htmlspecialchars($_SESSION['install_data']['database']['name'] ?? 'car_rental_db'); ?></p>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon">✓</div>
                <div class="summary-content">
                    <h4>الشركة</h4>
                    <p><?php echo htmlspecialchars($_SESSION['install_data']['company']['name'] ?? ''); ?></p>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon">✓</div>
                <div class="summary-content">
                    <h4>حساب المدير</h4>
                    <p><?php echo htmlspecialchars($_SESSION['install_data']['admin']['username'] ?? ''); ?></p>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon">✓</div>
                <div class="summary-content">
                    <h4>اللغة الافتراضية</h4>
                    <p><?php echo $_SESSION['install_data']['languages']['default'] === 'ar' ? 'العربية' : 'English'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="next-steps">
        <h3>الخطوات التالية</h3>
        <div class="steps-list">
            <div class="step-item-next">
                <div class="step-number-next">1</div>
                <div class="step-content-next">
                    <h4>تسجيل الدخول</h4>
                    <p>استخدم اسم المستخدم وكلمة المرور للدخول إلى لوحة التحكم</p>
                </div>
            </div>
            <div class="step-item-next">
                <div class="step-number-next">2</div>
                <div class="step-content-next">
                    <h4>إعداد النظام</h4>
                    <p>قم بإضافة السيارات، الفروع، المستخدمين، والإعدادات</p>
                </div>
            </div>
            <div class="step-item-next">
                <div class="step-number-next">3</div>
                <div class="step-content-next">
                    <h4>تخصيص الموقع</h4>
                    <p>قم بتحديث محتوى الصفحات وإعدادات الشركة</p>
                </div>
            </div>
            <div class="step-item-next">
                <div class="step-number-next">4</div>
                <div class="step-content-next">
                    <h4>الأمان</h4>
                    <p>⚠️ مهم: قم بحذف مجلد /install/ للأمان</p>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-warning">
        <strong>⚠️ تنبيه أمني مهم:</strong>
        <p>لأسباب أمنية، يُرجى حذف مجلد <code>/install/</code> كاملاً من السيرفر بعد اكتمال التثبيت.</p>
        <p>تم إنشاء ملف <code>install.lock</code> لمنع إعادة تشغيل المثبت.</p>
    </div>

    <div class="final-actions">
        <a href="../" class="btn btn-primary btn-large">
            🚀 الذهاب إلى الموقع
        </a>
        <a href="../admin/login" class="btn btn-secondary btn-large">
            🔐 الدخول إلى لوحة التحكم
        </a>
    </div>

    <?php else: ?>
    <!-- معاينة البيانات قبل التثبيت -->
    <div class="review-section">
        <h3>مراجعة البيانات</h3>
        <p>يرجى مراجعة جميع البيانات التالية قبل إتمام التثبيت</p>

        <div class="review-boxes">
            <!-- قاعدة البيانات -->
            <div class="review-box">
                <h4>🗄️ قاعدة البيانات</h4>
                <table class="review-table">
                    <tr>
                        <td><strong>المضيف:</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['install_data']['database']['host'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>اسم القاعدة:</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['install_data']['database']['name'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>المستخدم:</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['install_data']['database']['user'] ?? ''); ?></td>
                    </tr>
                </table>
            </div>

            <!-- معلومات الشركة -->
            <div class="review-box">
                <h4>🏢 معلومات الشركة</h4>
                <table class="review-table">
                    <tr>
                        <td><strong>الاسم:</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['install_data']['company']['name'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>البريد:</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['install_data']['company']['email'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>الهاتف:</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['install_data']['company']['phone'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>العملة:</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['install_data']['company']['currency'] ?? ''); ?></td>
                    </tr>
                </table>
            </div>

            <!-- حساب المدير -->
            <div class="review-box">
                <h4>👤 حساب المدير</h4>
                <table class="review-table">
                    <tr>
                        <td><strong>الاسم:</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['install_data']['admin']['full_name'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>اسم المستخدم:</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['install_data']['admin']['username'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>البريد:</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['install_data']['admin']['email'] ?? ''); ?></td>
                    </tr>
                </table>
            </div>

            <!-- إعدادات اللغة -->
            <div class="review-box">
                <h4>🌐 إعدادات اللغة</h4>
                <table class="review-table">
                    <tr>
                        <td><strong>اللغة الافتراضية:</strong></td>
                        <td><?php echo $_SESSION['install_data']['languages']['default'] === 'ar' ? 'العربية' : 'English'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>اللغات المفعلة:</strong></td>
                        <td>
                            <?php 
                            $langs = $_SESSION['install_data']['languages']['enabled'] ?? ['ar', 'en'];
                            echo implode(', ', array_map(function($l) {
                                return $l === 'ar' ? 'العربية' : 'English';
                            }, $langs));
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="info-box info-box-warning">
        <div class="info-icon">⚠️</div>
        <div class="info-content">
            <h4>تحذير</h4>
            <p>عند النقر على "إتمام التثبيت" سيتم:</p>
            <ul>
                <li>إنشاء جميع الجداول والبيانات الأساسية</li>
                <li>إنشاء ملف .env بمعلومات الاتصال</li>
                <li>إنشاء حساب المدير</li>
                <li>قفل معالج التثبيت</li>
            </ul>
            <p><strong>لا يمكن التراجع عن هذه العملية!</strong></p>
        </div>
    </div>

    <form method="POST" action="?step=6" id="finalInstallForm">
        <div class="step-actions">
            <a href="?step=5" class="btn btn-secondary">→ السابق</a>
            <button type="submit" class="btn btn-primary btn-large" id="installBtn">
                🚀 إتمام التثبيت
            </button>
        </div>
    </form>

    <script>
    document.getElementById('finalInstallForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('installBtn');
        btn.disabled = true;
        btn.innerHTML = '⏳ جارٍ التثبيت...';
    });
    </script>
    <?php endif; ?>
</div>

<style>
.success-animation {
    text-align: center;
    padding: 3rem 0;
    animation: fadeInUp 0.6s ease-out;
}

.success-icon {
    font-size: 5rem;
    margin-bottom: 1rem;
    animation: bounce 1s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.installation-summary {
    margin: 2rem 0;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: #f0fdf4;
    border: 2px solid #10b981;
    border-radius: 8px;
}

.summary-icon {
    font-size: 2rem;
    color: #10b981;
}

.summary-content h4 {
    margin: 0 0 0.25rem 0;
    color: #065f46;
}

.summary-content p {
    margin: 0;
    color: #047857;
}

.next-steps {
    margin: 2rem 0;
}

.steps-list {
    margin-top: 1rem;
}

.step-item-next {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.step-number-next {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #2563eb;
    color: white;
    border-radius: 50%;
    font-weight: bold;
}

.step-content-next h4 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
}

.step-content-next p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.final-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin: 2rem 0;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.125rem;
}

.review-boxes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.review-box {
    padding: 1.5rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.review-box h4 {
    margin: 0 0 1rem 0;
    color: #1f2937;
    font-size: 1.125rem;
}

.review-table {
    width: 100%;
}

.review-table td {
    padding: 0.5rem 0;
    color: #6b7280;
}

.review-table td:first-child {
    width: 40%;
}
</style>