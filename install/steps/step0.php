<?php
/**
 * File: step0.php
 * Path: /install/steps/step0.php
 * Purpose: Language selection for installer
 * Dependencies: None
 */

$texts = [
    'ar' => [
        'title' => 'مرحباً بك في معالج التثبيت',
        'subtitle' => 'اختر لغتك المفضلة للمتابعة',
        'description' => 'يمكنك تغيير لغة المثبت في أي وقت من الزر أعلى الصفحة',
        'next' => 'المتابعة',
        'note_title' => 'ملاحظة مهمة',
        'note_text' => 'هذا فقط لغة واجهة المثبت. ستتمكن من إضافة وإدارة لغات النظام في الخطوات القادمة.'
    ],
    'en' => [
        'title' => 'Welcome to Installation Wizard',
        'subtitle' => 'Choose your preferred language to continue',
        'description' => 'You can change the installer language anytime from the button at the top',
        'next' => 'Continue',
        'note_title' => 'Important Note',
        'note_text' => 'This is only for the installer interface. You will be able to add and manage system languages in the following steps.'
    ]
];

$currentLang = $lang ?? 'ar';
$text = $texts[$currentLang];

// حفظ اللغة المختارة في الجلسة عند النقر
if (!isset($_SESSION['installer_lang'])) {
    $_SESSION['installer_lang'] = $currentLang;
}
?>

<div class="step-content">
    <div class="language-selection-page">
        <div class="welcome-icon">
            🎉
        </div>
        
        <h2><?php echo $text['title']; ?></h2>
        <p class="welcome-subtitle"><?php echo $text['subtitle']; ?></p>

        <div class="language-cards">
            <a href="index.php?step=0&lang=ar" class="language-card-big <?php echo $currentLang === 'ar' ? 'selected' : ''; ?>">
                <div class="lang-flag">🇸🇦</div>
                <div class="lang-name">العربية</div>
                <div class="lang-name-en">Arabic</div>
                <div class="lang-direction">RTL →</div>
                <?php if ($currentLang === 'ar'): ?>
                <div class="selected-badge">✓</div>
                <?php endif; ?>
            </a>

            <a href="index.php?step=0&lang=en" class="language-card-big <?php echo $currentLang === 'en' ? 'selected' : ''; ?>">
                <div class="lang-flag">🇬🇧</div>
                <div class="lang-name">English</div>
                <div class="lang-name-en">الإنجليزية</div>
                <div class="lang-direction">← LTR</div>
                <?php if ($currentLang === 'en'): ?>
                <div class="selected-badge">✓</div>
                <?php endif; ?>
            </a>
        </div>

        <div class="info-box info-box-info">
            <div class="info-icon">ℹ️</div>
            <div class="info-content">
                <h4><?php echo $text['note_title']; ?></h4>
                <p><?php echo $text['note_text']; ?></p>
            </div>
        </div>

        <p class="language-hint"><?php echo $text['description']; ?></p>

        <div class="step-actions">
            <a href="index.php?step=1b" class="btn btn-primary btn-large" onclick="sessionStorage.setItem('installerLangSet', 'true');">
                <?php echo $currentLang === 'ar' ? 'البدء في التثبيت ←' : 'Start Installation →'; ?>
            </a>
        </div>
    </div>
</div>

<style>
.language-selection-page {
    text-align: center;
    padding: 2rem 0;
    max-width: 800px;
    margin: 0 auto;
}

.welcome-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

.welcome-subtitle {
    font-size: 1.125rem;
    color: var(--gray-600);
    margin-bottom: 3rem;
}

.language-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin: 3rem 0;
}

.language-card-big {
    position: relative;
    padding: 3rem 2rem;
    background: white;
    border: 3px solid var(--gray-200);
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
}

.language-card-big:hover {
    border-color: var(--primary-color);
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
}

.language-card-big.selected {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.25);
}

.lang-flag {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.lang-name {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 0.5rem;
}

.lang-name-en {
    font-size: 1rem;
    color: var(--gray-500);
    margin-bottom: 1rem;
}

.lang-direction {
    font-size: 0.875rem;
    color: var(--gray-400);
    font-weight: 600;
    text-transform: uppercase;
}

.selected-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 40px;
    height: 40px;
    background: var(--success-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    animation: scaleIn 0.3s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
    }
    to {
        transform: scale(1);
    }
}

[dir="ltr"] .selected-badge {
    right: auto;
    left: 1rem;
}

.language-hint {
    color: var(--gray-500);
    font-size: 0.875rem;
    margin: 2rem 0;
}

.btn-large {
    padding: 1rem 3rem;
    font-size: 1.125rem;
    min-width: 250px;
}

@media (max-width: 768px) {
    .language-cards {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .language-card-big {
        padding: 2rem 1.5rem;
    }
    
    .lang-flag {
        font-size: 3rem;
    }
    
    .lang-name {
        font-size: 1.5rem;
    }
}
</style>