<?php
/**
 * File: step3-company.php
 * Path: /install/steps/step3-company.php
 * Purpose: Company information setup
 * Dependencies: Database connection from step 2
 */

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyName = $_POST['company_name'] ?? '';
    $companyNameEn = $_POST['company_name_en'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? 'Saudi Arabia';
    $currency = $_POST['currency'] ?? 'SAR';
    $timezone = $_POST['timezone'] ?? 'Asia/Riyadh';

    // التحقق من البيانات
    if (empty($companyName)) $errors[] = 'اسم الشركة بالعربي مطلوب';
    if (empty($companyNameEn)) $errors[] = 'اسم الشركة بالإنجليزي مطلوب';
    if (empty($email)) $errors[] = 'البريد الإلكتروني مطلوب';
    if (empty($phone)) $errors[] = 'رقم الهاتف مطلوب';

    if (empty($errors)) {
        // حفظ البيانات في الجلسة
        $_SESSION['install_data']['company'] = [
            'name' => $companyName,
            'name_en' => $companyNameEn,
            'email' => $email,
            'phone' => $phone,
            'whatsapp' => $whatsapp,
            'address' => $address,
            'city' => $city,
            'country' => $country,
            'currency' => $currency,
            'timezone' => $timezone
        ];
        $success = true;
    }
}
?>

<div class="step-content">
    <div class="step-header">
        <h2>🏢 الخطوة 3: معلومات الشركة</h2>
        <p>قم بإدخال معلومات شركتك أو مؤسستك</p>
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
        <strong>نجح!</strong> تم حفظ معلومات الشركة بنجاح.
    </div>
    <div class="step-actions">
        <a href="?step=2" class="btn btn-secondary">→ السابق</a>
        <a href="?step=4" class="btn btn-primary">التالي: حساب المدير ←</a>
    </div>
    <?php else: ?>
    <form method="POST" action="?step=3" class="install-form">
        <div class="form-section">
            <h3>معلومات أساسية</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="company_name">اسم الشركة (بالعربي) *</label>
                    <input type="text" 
                           id="company_name" 
                           name="company_name" 
                           value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="company_name_en">Company Name (English) *</label>
                    <input type="text" 
                           id="company_name_en" 
                           name="company_name_en" 
                           value="<?php echo htmlspecialchars($_POST['company_name_en'] ?? ''); ?>" 
                           required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>معلومات الاتصال</h3>
            
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
                    <label for="phone">رقم الهاتف *</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                           placeholder="+966XXXXXXXXX"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label for="whatsapp">رقم الواتساب</label>
                <input type="tel" 
                       id="whatsapp" 
                       name="whatsapp" 
                       value="<?php echo htmlspecialchars($_POST['whatsapp'] ?? ''); ?>" 
                       placeholder="+966XXXXXXXXX">
                <small>سيتم استخدامه لإرسال التنبيهات</small>
            </div>
        </div>

        <div class="form-section">
            <h3>العنوان والموقع</h3>
            
            <div class="form-group">
                <label for="address">العنوان</label>
                <textarea id="address" 
                          name="address" 
                          rows="3"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city">المدينة</label>
                    <input type="text" 
                           id="city" 
                           name="city" 
                           value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="country">الدولة</label>
                    <select id="country" name="country">
                        <option value="Saudi Arabia" selected>المملكة العربية السعودية</option>
                        <option value="United Arab Emirates">الإمارات العربية المتحدة</option>
                        <option value="Kuwait">الكويت</option>
                        <option value="Qatar">قطر</option>
                        <option value="Bahrain">البحرين</option>
                        <option value="Oman">عُمان</option>
                        <option value="Jordan">الأردن</option>
                        <option value="Egypt">مصر</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>إعدادات النظام</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="currency">العملة</label>
                    <select id="currency" name="currency">
                        <option value="SAR" selected>ريال سعودي (SAR)</option>
                        <option value="AED">درهم إماراتي (AED)</option>
                        <option value="KWD">دينار كويتي (KWD)</option>
                        <option value="QAR">ريال قطري (QAR)</option>
                        <option value="BHD">دينار بحريني (BHD)</option>
                        <option value="OMR">ريال عُماني (OMR)</option>
                        <option value="JOD">دينار أردني (JOD)</option>
                        <option value="EGP">جنيه مصري (EGP)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="timezone">المنطقة الزمنية</label>
                    <select id="timezone" name="timezone">
                        <option value="Asia/Riyadh" selected>الرياض (GMT+3)</option>
                        <option value="Asia/Dubai">دبي (GMT+4)</option>
                        <option value="Asia/Kuwait">الكويت (GMT+3)</option>
                        <option value="Asia/Qatar">قطر (GMT+3)</option>
                        <option value="Asia/Bahrain">البحرين (GMT+3)</option>
                        <option value="Asia/Muscat">مسقط (GMT+4)</option>
                        <option value="Asia/Amman">عمّان (GMT+3)</option>
                        <option value="Africa/Cairo">القاهرة (GMT+2)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="step-actions">
            <a href="?step=2" class="btn btn-secondary">→ السابق</a>
            <button type="submit" class="btn btn-primary">
                حفظ والمتابعة ←
            </button>
        </div>
    </form>
    <?php endif; ?>
</div>