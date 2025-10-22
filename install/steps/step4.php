<?php
/**
 * File: step4-admin.php
 * Path: /install/steps/step4-admin.php
 * Purpose: Create admin account
 * Dependencies: Database connection
 */

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // التحقق من البيانات
    if (empty($fullName)) $errors[] = 'الاسم الكامل مطلوب';
    if (empty($username)) $errors[] = 'اسم المستخدم مطلوب';
    elseif (strlen($username) < 3) $errors[] = 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل';
    elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) $errors[] = 'اسم المستخدم يجب أن يحتوي على حروف وأرقام فقط';
    
    if (empty($email)) $errors[] = 'البريد الإلكتروني مطلوب';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد الإلكتروني غير صحيح';
    
    if (empty($password)) $errors[] = 'كلمة المرور مطلوبة';
    elseif (strlen($password) < 8) $errors[] = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
    elseif (!preg_match('/[A-Z]/', $password)) $errors[] = 'كلمة المرور يجب أن تحتوي على حرف كبير واحد على الأقل';
    elseif (!preg_match('/[a-z]/', $password)) $errors[] = 'كلمة المرور يجب أن تحتوي على حرف صغير واحد على الأقل';
    elseif (!preg_match('/[0-9]/', $password)) $errors[] = 'كلمة المرور يجب أن تحتوي على رقم واحد على الأقل';
    
    if ($password !== $confirmPassword) $errors[] = 'كلمات المرور غير متطابقة';

    if (empty($errors)) {
        // حفظ البيانات في الجلسة
        $_SESSION['install_data']['admin'] = [
            'full_name' => $fullName,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])
        ];
        $success = true;
    }
}
?>

<div class="step-content">
    <div class="step-header">
        <h2>👤 الخطوة 4: حساب المدير</h2>
        <p>قم بإنشاء حساب المدير الرئيسي للنظام</p>
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
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <strong>نجح!</strong> تم إنشاء حساب المدير بنجاح.
    </div>
    <div class="step-actions">
        <a href="?step=3" class="btn btn-secondary">→ السابق</a>
        <a href="?step=5" class="btn btn-primary">التالي: إعدادات اللغة ←</a>
    </div>
    <?php else: ?>
    <form method="POST" action="?step=4" class="install-form" id="adminForm">
        <div class="form-section">
            <h3>معلومات شخصية</h3>
            
            <div class="form-group">
                <label for="full_name">الاسم الكامل *</label>
                <input type="text" 
                       id="full_name" 
                       name="full_name" 
                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                       required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">البريد الإلكتروني *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="phone">رقم الهاتف</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                           placeholder="+966XXXXXXXXX">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>معلومات تسجيل الدخول</h3>
            
            <div class="form-group">
                <label for="username">اسم المستخدم *</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       pattern="[a-zA-Z0-9_]+"
                       minlength="3"
                       required>
                <small>حروف إنجليزية وأرقام فقط، بدون مسافات (3 أحرف على الأقل)</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">كلمة المرور *</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           minlength="8"
                           required>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">تأكيد كلمة المرور *</label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           minlength="8"
                           required>
                    <div class="password-match" id="passwordMatch"></div>
                </div>
            </div>
        </div>

        <div class="info-box info-box-info">
            <div class="info-icon">🔐</div>
            <div class="info-content">
                <h4>متطلبات كلمة المرور</h4>
                <ul class="password-requirements">
                    <li id="req-length">✗ 8 أحرف على الأقل</li>
                    <li id="req-upper">✗ حرف كبير واحد على الأقل (A-Z)</li>
                    <li id="req-lower">✗ حرف صغير واحد على الأقل (a-z)</li>
                    <li id="req-number">✗ رقم واحد على الأقل (0-9)</li>
                </ul>
            </div>
        </div>

        <div class="step-actions">
            <a href="?step=3" class="btn btn-secondary">→ السابق</a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                إنشاء الحساب والمتابعة ←
            </button>
        </div>
    </form>

    <script>
    // التحقق من قوة كلمة المرور في الوقت الفعلي
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strength = document.getElementById('passwordStrength');
        const requirements = {
            length: password.length >= 8,
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /[0-9]/.test(password)
        };

        // تحديث متطلبات كلمة المرور
        document.getElementById('req-length').className = requirements.length ? 'valid' : '';
        document.getElementById('req-length').textContent = (requirements.length ? '✓' : '✗') + ' 8 أحرف على الأقل';
        
        document.getElementById('req-upper').className = requirements.upper ? 'valid' : '';
        document.getElementById('req-upper').textContent = (requirements.upper ? '✓' : '✗') + ' حرف كبير واحد على الأقل (A-Z)';
        
        document.getElementById('req-lower').className = requirements.lower ? 'valid' : '';
        document.getElementById('req-lower').textContent = (requirements.lower ? '✓' : '✗') + ' حرف صغير واحد على الأقل (a-z)';
        
        document.getElementById('req-number').className = requirements.number ? 'valid' : '';
        document.getElementById('req-number').textContent = (requirements.number ? '✓' : '✗') + ' رقم واحد على الأقل (0-9)';

        // حساب قوة كلمة المرور
        const score = Object.values(requirements).filter(Boolean).length;
        
        strength.className = 'password-strength';
        if (score === 0) {
            strength.textContent = '';
        } else if (score <= 2) {
            strength.classList.add('weak');
            strength.textContent = 'ضعيفة';
        } else if (score === 3) {
            strength.classList.add('medium');
            strength.textContent = 'متوسطة';
        } else {
            strength.classList.add('strong');
            strength.textContent = 'قوية';
        }
    });

    // التحقق من تطابق كلمات المرور
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const match = document.getElementById('passwordMatch');
        
        if (this.value === '') {
            match.textContent = '';
            match.className = 'password-match';
        } else if (this.value === password) {
            match.textContent = '✓ كلمات المرور متطابقة';
            match.className = 'password-match match';
        } else {
            match.textContent = '✗ كلمات المرور غير متطابقة';
            match.className = 'password-match no-match';
        }
    });
    </script>
    <?php endif; ?>
</div>