/**
 * File: install.js
 * Path: /install/assets/js/install.js
 * Purpose: Installation wizard interactive functionality
 * Dependencies: None (Vanilla JS)
 */

// انتظار تحميل الصفحة بالكامل
document.addEventListener('DOMContentLoaded', function() {
    
    // تفعيل التحقق من النماذج
    initFormValidation();
    
    // إضافة تأثيرات بصرية
    initAnimations();
    
    // معالجة أزرار التنقل
    initNavigationButtons();
    
    // التحقق من قوة كلمة المرور
    initPasswordStrength();
    
    // معالجة اختيار اللغات
    initLanguageSelection();
});

/**
 * تفعيل التحقق من النماذج
 */
function initFormValidation() {
    const forms = document.querySelectorAll('.install-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showError('يرجى ملء جميع الحقول المطلوبة بشكل صحيح');
            }
        });
        
        // التحقق الفوري من الحقول
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

/**
 * التحقق من النموذج
 */
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * التحقق من حقل واحد
 */
function validateField(field) {
    let isValid = true;
    let errorMessage = '';
    
    // إزالة رسائل الخطأ السابقة
    removeFieldError(field);
    
    // التحقق من أن الحقل غير فارغ
    if (field.hasAttribute('required') && !field.value.trim()) {
        isValid = false;
        errorMessage = 'هذا الحقل مطلوب';
    }
    
    // التحقق من البريد الإلكتروني
    if (field.type === 'email' && field.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(field.value)) {
            isValid = false;
            errorMessage = 'البريد الإلكتروني غير صحيح';
        }
    }
    
    // التحقق من كلمة المرور
    if (field.type === 'password' && field.value) {
        if (field.value.length < 8) {
            isValid = false;
            errorMessage = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
        }
    }
    
    // التحقق من تطابق كلمة المرور
    if (field.name === 'confirm_password') {
        const password = document.querySelector('input[name="password"]');
        if (password && field.value !== password.value) {
            isValid = false;
            errorMessage = 'كلمات المرور غير متطابقة';
        }
    }
    
    // عرض رسالة الخطأ
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        field.classList.add('valid');
    }
    
    return isValid;
}

/**
 * عرض رسالة خطأ للحقل
 */
function showFieldError(field, message) {
    field.classList.add('error');
    field.classList.remove('valid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = '#ef4444';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    
    field.parentNode.appendChild(errorDiv);
}

/**
 * إزالة رسالة خطأ الحقل
 */
function removeFieldError(field) {
    field.classList.remove('error', 'valid');
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * عرض رسالة خطأ عامة
 */
function showError(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-error';
    alert.innerHTML = `<strong>خطأ:</strong> ${message}`;
    alert.style.animation = 'slideDown 0.3s ease-out';
    
    const content = document.querySelector('.step-content');
    content.insertBefore(alert, content.firstChild);
    
    // إزالة التنبيه بعد 5 ثواني
    setTimeout(() => {
        alert.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}

/**
 * إضافة تأثيرات بصرية
 */
function initAnimations() {
    // تأثير التمرير السلس
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // تأثير التركيز على الحقول
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentNode.classList.remove('focused');
        });
    });
}

/**
 * معالجة أزرار التنقل
 */
function initNavigationButtons() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            // إضافة تأثير التحميل للأزرار
            if (this.type === 'submit') {
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span>⏳ جارٍ المعالجة...</span>';
                
                // استعادة النص الأصلي في حالة الخطأ
                setTimeout(() => {
                    if (this.disabled) {
                        this.disabled = false;
                        this.innerHTML = originalText;
                    }
                }, 10000);
            }
        });
    });
}

/**
 * التحقق من قوة كلمة المرور
 */
function initPasswordStrength() {
    const passwordInput = document.querySelector('input[name="password"]');
    if (!passwordInput) return;
    
    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });
    
    // التحقق من تطابق كلمة المرور
    const confirmInput = document.querySelector('input[name="confirm_password"]');
    if (confirmInput) {
        confirmInput.addEventListener('input', function() {
            checkPasswordMatch(passwordInput.value, this.value);
        });
    }
}

/**
 * فحص قوة كلمة المرور
 */
function checkPasswordStrength(password) {
    const requirements = {
        length: password.length >= 8,
        upper: /[A-Z]/.test(password),
        lower: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    // حساب النقاط
    const score = Object.values(requirements).filter(Boolean).length;
    
    // تحديث مؤشر القوة
    const strengthDiv = document.getElementById('passwordStrength');
    if (strengthDiv) {
        strengthDiv.className = 'password-strength';
        
        if (score === 0) {
            strengthDiv.textContent = '';
        } else if (score <= 2) {
            strengthDiv.classList.add('weak');
            strengthDiv.textContent = 'ضعيفة';
        } else if (score === 3 || score === 4) {
            strengthDiv.classList.add('medium');
            strengthDiv.textContent = 'متوسطة';
        } else {
            strengthDiv.classList.add('strong');
            strengthDiv.textContent = 'قوية جداً';
        }
    }
    
    // تحديث قائمة المتطلبات
    updateRequirements(requirements);
}

/**
 * تحديث قائمة متطلبات كلمة المرور
 */
function updateRequirements(requirements) {
    const reqElements = {
        length: document.getElementById('req-length'),
        upper: document.getElementById('req-upper'),
        lower: document.getElementById('req-lower'),
        number: document.getElementById('req-number')
    };
    
    for (const [key, element] of Object.entries(reqElements)) {
        if (element) {
            if (requirements[key]) {
                element.classList.add('valid');
                element.textContent = element.textContent.replace('✗', '✓');
            } else {
                element.classList.remove('valid');
                element.textContent = element.textContent.replace('✓', '✗');
            }
        }
    }
}

/**
 * فحص تطابق كلمات المرور
 */
function checkPasswordMatch(password, confirm) {
    const matchDiv = document.getElementById('passwordMatch');
    if (!matchDiv) return;
    
    matchDiv.className = 'password-match';
    
    if (confirm === '') {
        matchDiv.textContent = '';
    } else if (confirm === password) {
        matchDiv.classList.add('match');
        matchDiv.textContent = '✓ كلمات المرور متطابقة';
    } else {
        matchDiv.classList.add('no-match');
        matchDiv.textContent = '✗ كلمات المرور غير متطابقة';
    }
}

/**
 * معالجة اختيار اللغات
 */
function initLanguageSelection() {
    // التأكد من اختيار اللغة الافتراضية ضمن اللغات المفعلة
    const defaultRadios = document.querySelectorAll('input[name="default_language"]');
    const enabledCheckboxes = document.querySelectorAll('input[name="enabled_languages[]"]');
    
    if (defaultRadios.length > 0 && enabledCheckboxes.length > 0) {
        defaultRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // التأكد من أن اللغة الافتراضية مفعلة
                const langCode = this.value;
                const checkbox = document.querySelector(`input[name="enabled_languages[]"][value="${langCode}"]`);
                if (checkbox && !checkbox.checked) {
                    checkbox.checked = true;
                }
            });
        });
        
        // منع إلغاء تفعيل اللغة الافتراضية
        enabledCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const langCode = this.value;
                const isDefault = document.querySelector(`input[name="default_language"][value="${langCode}"]`)?.checked;
                
                if (isDefault && !this.checked) {
                    this.checked = true;
                    showWarning('لا يمكن إلغاء تفعيل اللغة الافتراضية');
                }
                
                // التأكد من اختيار لغة واحدة على الأقل
                const checkedCount = document.querySelectorAll('input[name="enabled_languages[]"]:checked').length;
                if (checkedCount === 0) {
                    this.checked = true;
                    showWarning('يجب اختيار لغة واحدة على الأقل');
                }
            });
        });
    }
}

/**
 * عرض تحذير
 */
function showWarning(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-warning';
    alert.innerHTML = `<strong>تنبيه:</strong> ${message}`;
    alert.style.animation = 'slideDown 0.3s ease-out';
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.left = '20px';
    alert.style.zIndex = '9999';
    alert.style.maxWidth = '500px';
    alert.style.margin = '0 auto';
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

/**
 * معالج تقدم التثبيت في الخطوة الأخيرة
 */
function showInstallProgress() {
    const steps = [
        'إنشاء قاعدة البيانات...',
        'إدخال اللغات والترجمات...',
        'إنشاء الأدوار والصلاحيات...',
        'إنشاء الفرع الرئيسي...',
        'إنشاء حساب المدير...',
        'حفظ الإعدادات...',
        'إنشاء الصفحات الثابتة...',
        'إنشاء ملف التكوين...',
        'اكتمال التثبيت!'
    ];
    
    let currentStep = 0;
    const progressDiv = document.createElement('div');
    progressDiv.className = 'install-progress';
    progressDiv.style.cssText = `
        padding: 1.5rem;
        background: white;
        border-radius: 8px;
        margin: 1rem 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    `;
    
    const progressBar = document.createElement('div');
    progressBar.style.cssText = `
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 1rem;
    `;
    
    const progressFill = document.createElement('div');
    progressFill.style.cssText = `
        height: 100%;
        background: linear-gradient(90deg, #2563eb, #10b981);
        width: 0%;
        transition: width 0.5s ease;
    `;
    
    const progressText = document.createElement('div');
    progressText.style.cssText = `
        text-align: center;
        color: #6b7280;
        font-size: 0.875rem;
    `;
    
    progressBar.appendChild(progressFill);
    progressDiv.appendChild(progressBar);
    progressDiv.appendChild(progressText);
    
    const form = document.getElementById('finalInstallForm');
    if (form) {
        form.parentNode.insertBefore(progressDiv, form);
    }
    
    // تحديث التقدم
    const interval = setInterval(() => {
        if (currentStep < steps.length) {
            progressText.textContent = steps[currentStep];
            progressFill.style.width = ((currentStep + 1) / steps.length * 100) + '%';
            currentStep++;
        } else {
            clearInterval(interval);
        }
    }, 800);
}

// إضافة أنماط CSS إضافية ديناميكياً
const style = document.createElement('style');
style.textContent = `
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
    
    input.error, textarea.error, select.error {
        border-color: #ef4444 !important;
    }
    
    input.valid, textarea.valid, select.valid {
        border-color: #10b981 !important;
    }
    
    .form-group.focused label {
        color: #2563eb;
    }
`;
document.head.appendChild(style);