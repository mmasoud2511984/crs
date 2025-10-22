<?php
/**
 * File: step5-languages.php
 * Path: /install/steps/step5-languages.php
 * Purpose: Language configuration
 * Dependencies: Database connection
 */

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $defaultLang = $_POST['default_language'] ?? 'ar';
    $enabledLanguages = $_POST['enabled_languages'] ?? ['ar', 'en'];

    // حفظ البيانات في الجلسة
    $_SESSION['install_data']['languages'] = [
        'default' => $defaultLang,
        'enabled' => $enabledLanguages
    ];
    $success = true;
}

$languages = [
    'ar' => [
        'name' => 'العربية',
        'name_en' => 'Arabic',
        'direction' => 'rtl',
        'flag' => '🇸🇦',
        'recommended' => true
    ],
    'en' => [
        'name' => 'الإنجليزية',
        'name_en' => 'English',
        'direction' => 'ltr',
        'flag' => '🇬🇧',
        'recommended' => true
    ],
    'fr' => [
        'name' => 'الفرنسية',
        'name_en' => 'French',
        'direction' => 'ltr',
        'flag' => '🇫🇷',
        'recommended' => false
    ],
    'es' => [
        'name' => 'الإسبانية',
        'name_en' => 'Spanish',
        'direction' => 'ltr',
        'flag' => '🇪🇸',
        'recommended' => false
    ],
    'de' => [
        'name' => 'الألمانية',
        'name_en' => 'German',
        'direction' => 'ltr',
        'flag' => '🇩🇪',
        'recommended' => false
    ],
    'tr' => [
        'name' => 'التركية',
        'name_en' => 'Turkish',
        'direction' => 'ltr',
        'flag' => '🇹🇷',
        'recommended' => false
    ],
    'ur' => [
        'name' => 'الأردية',
        'name_en' => 'Urdu',
        'direction' => 'rtl',
        'flag' => '🇵🇰',
        'recommended' => false
    ]
];
?>

<div class="step-content">
    <div class="step-header">
        <h2>🌐 الخطوة 5: إعدادات اللغة</h2>
        <p>اختر اللغات التي تريد تفعيلها في النظام</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <strong>نجح!</strong> تم حفظ إعدادات اللغة بنجاح.
    </div>
    <div class="step-actions">
        <a href="?step=4" class="btn btn-secondary">→ السابق</a>
        <a href="?step=6" class="btn btn-primary">التالي: إنهاء التثبيت ←</a>
    </div>
    <?php else: ?>
    <form method="POST" action="?step=5" class="install-form">
        <div class="form-section">
            <h3>اللغة الافتراضية</h3>
            <p>اللغة التي سيتم عرضها بشكل افتراضي للمستخدمين</p>
            
            <div class="language-selector">
                <label class="language-option">
                    <input type="radio" name="default_language" value="ar" checked>
                    <div class="language-card">
                        <span class="language-flag">🇸🇦</span>
                        <div class="language-info">
                            <h4>العربية</h4>
                            <p>Arabic</p>
                        </div>
                        <span class="badge badge-success">موصى به</span>
                    </div>
                </label>

                <label class="language-option">
                    <input type="radio" name="default_language" value="en">
                    <div class="language-card">
                        <span class="language-flag">🇬🇧</span>
                        <div class="language-info">
                            <h4>الإنجليزية</h4>
                            <p>English</p>
                        </div>
                        <span class="badge badge-success">موصى به</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3>اللغات المتاحة</h3>
            <p>يمكنك تفعيل لغات إضافية (يمكن إضافة المزيد لاحقاً من لوحة التحكم)</p>
            
            <div class="languages-grid">
                <?php foreach ($languages as $code => $lang): ?>
                <label class="language-checkbox">
                    <input type="checkbox" 
                           name="enabled_languages[]" 
                           value="<?php echo $code; ?>"
                           <?php echo in_array($code, ['ar', 'en']) ? 'checked' : ''; ?>>
                    <div class="language-card small">
                        <span class="language-flag"><?php echo $lang['flag']; ?></span>
                        <div class="language-info">
                            <h5><?php echo $lang['name']; ?></h5>
                            <p><?php echo $lang['name_en']; ?></p>
                        </div>
                        <?php if ($lang['recommended']): ?>
                        <span class="badge badge-info">موصى به</span>
                        <?php endif; ?>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="info-box info-box-info">
            <div class="info-icon">ℹ️</div>
            <div class="info-content">
                <h4>معلومات مهمة</h4>
                <ul>
                    <li>اللغة العربية والإنجليزية موصى بهما ومفعّلان افتراضياً</li>
                    <li>يمكن إضافة وإدارة اللغات لاحقاً من لوحة التحكم</li>
                    <li>سيتم تحميل الترجمات الأساسية للغات المختارة</li>
                    <li>يمكن للمستخدمين اختيار لغتهم المفضلة من حساباتهم</li>
                </ul>
            </div>
        </div>

        <div class="form-section">
            <h3>إعدادات إضافية</h3>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="auto_detect_language" checked>
                    الكشف التلقائي عن لغة المتصفح
                </label>
                <small>سيحاول النظام عرض اللغة المناسبة حسب لغة متصفح الزائر</small>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="show_language_switcher" checked>
                    عرض أداة تبديل اللغة في الواجهة
                </label>
                <small>سيتمكن المستخدمون من تغيير اللغة بسهولة</small>
            </div>
        </div>

        <div class="step-actions">
            <a href="?step=4" class="btn btn-secondary">→ السابق</a>
            <button type="submit" class="btn btn-primary">
                حفظ والمتابعة ←
            </button>
        </div>
    </form>
    <?php endif; ?>
</div>

<style>
.language-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.language-option {
    cursor: pointer;
}

.language-option input[type="radio"] {
    display: none;
}

.language-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.3s;
}

.language-option input[type="radio"]:checked + .language-card {
    border-color: #2563eb;
    background-color: #eff6ff;
}

.language-card:hover {
    border-color: #2563eb;
}

.language-flag {
    font-size: 2rem;
}

.language-info h4, .language-info h5 {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
}

.language-info p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.languages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.language-checkbox {
    cursor: pointer;
}

.language-checkbox input[type="checkbox"] {
    display: none;
}

.language-card.small {
    padding: 1rem;
}

.language-checkbox input[type="checkbox"]:checked + .language-card {
    border-color: #10b981;
    background-color: #f0fdf4;
}

.language-checkbox input[type="checkbox"]:disabled + .language-card {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>