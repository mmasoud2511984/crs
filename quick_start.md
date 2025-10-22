# 🚀 دليل البدء السريع - نظام إدارة تأجير السيارات

## ⚡ التثبيت السريع (5 دقائق)

### الطريقة الأولى: باستخدام السكريبت التلقائي

```bash
# 1. تحميل الملفات
cd /var/www/html
git clone [repository-url] car-rental-system
cd car-rental-system

# 2. منح صلاحيات التنفيذ
chmod +x create-structure.sh
chmod +x install.sh

# 3. تشغيل سكريبت إنشاء الهيكل
./create-structure.sh

# 4. تشغيل سكريبت التثبيت
./install.sh

# 5. افتح المتصفح
http://yourdomain.com/install/
```

---

### الطريقة الثانية: التثبيت اليدوي

#### 1️⃣ إنشاء المجلدات

```bash
# إنشاء هيكل المجلدات الكامل
./create-structure.sh
```

#### 2️⃣ تثبيت الاعتماديات

```bash
# تثبيت Composer packages
composer install

# نسخ ملف البيئة
cp .env.example .env
```

#### 3️⃣ إعداد قاعدة البيانات

```bash
# الدخول إلى MySQL
mysql -u root -p

# إنشاء قاعدة البيانات
CREATE DATABASE car_rental_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# استيراد الهيكل والبيانات
mysql -u root -p car_rental_db < database/schema.sql
mysql -u root -p car_rental_db < database/seeds/languages.sql
mysql -u root -p car_rental_db < database/seeds/translations.sql
mysql -u root -p car_rental_db < database/seeds/roles_permissions.sql
mysql -u root -p car_rental_db < database/seeds/settings.sql
mysql -u root -p car_rental_db < database/seeds/pages.sql
```

#### 4️⃣ تعديل ملف .env

```bash
nano .env
```

عدّل الإعدادات التالية:

```env
DB_HOST=localhost
DB_DATABASE=car_rental_db
DB_USERNAME=root
DB_PASSWORD=your_password

APP_URL=http://yourdomain.com
ENCRYPTION_KEY=[generate-random-key]
```

#### 5️⃣ ضبط الصلاحيات

```bash
chmod -R 755 .
chmod -R 777 storage/
chmod -R 777 public/uploads/
```

#### 6️⃣ إكمال التثبيت

افتح المتصفح وانتقل إلى:
```
http://yourdomain.com/install/
```

---

## 📋 قائمة التحقق السريع

قبل البدء، تأكد من:

- [ ] PHP 8.2 أو أحدث مثبت
- [ ] MySQL 8.0 أو أحدث مثبت
- [ ] Composer مثبت
- [ ] Apache/Nginx مع mod_rewrite
- [ ] جميع امتدادات PHP المطلوبة متوفرة

---

## 🔧 إعداد Apache

أضف إلى ملف VirtualHost:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/car-rental-system
    
    <Directory /var/www/html/car-rental-system>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/car-rental-error.log
    CustomLog ${APACHE_LOG_DIR}/car-rental-access.log combined
</VirtualHost>
```

ثم:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## 🔧 إعداد Nginx

أضف إلى ملف الإعداد:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/html/car-rental-system;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

ثم:
```bash
sudo systemctl restart nginx
```

---

## ⏰ إعداد Cron Jobs

```bash
crontab -e
```

أضف السطور التالية:

```cron
# إرسال تذكيرات الحجوزات (كل ساعة)
0 * * * * php /path/to/cron/send-rental-reminders.php

# فحص الصيانة المستحقة (يومياً الساعة 8 صباحاً)
0 8 * * * php /path/to/cron/check-maintenance-due.php

# فحص انتهاء صلاحية الوثائق (يومياً)
0 9 * * * php /path/to/cron/check-document-expiry.php

# معالجة طابور واتساب (كل 5 دقائق)
*/5 * * * * php /path/to/cron/process-whatsapp-queue.php

# تنظيف الجلسات القديمة (يومياً الساعة 3 صباحاً)
0 3 * * * php /path/to/cron/cleanup-sessions.php

# نسخ احتياطي (يومياً الساعة 2 صباحاً)
0 2 * * * php /path/to/cron/backup-database.php
```

---

## 🎯 معالج التثبيت - 6 خطوات

### الخطوة 1: فحص المتطلبات
- فحص إصدار PHP
- فحص امتدادات PHP
- فحص صلاحيات المجلدات
- فحص اتصال قاعدة البيانات

### الخطوة 2: إعداد قاعدة البيانات
- تأكيد معلومات الاتصال
- اختبار الاتصال
- إنشاء الجداول (إذا لم تكن موجودة)

### الخطوة 3: معلومات الشركة
- اسم الشركة
- البريد الإلكتروني
- رقم الهاتف
- العنوان
- الشعار (اختياري)

### الخطوة 4: حساب المدير
- اسم المستخدم
- البريد الإلكتروني
- كلمة المرور
- الاسم الكامل

### الخطوة 5: إعدادات اللغة
- اختيار اللغة الافتراضية
- تفعيل/تعطيل اللغات
- إعدادات العملة

### الخطوة 6: إنهاء التثبيت
- مراجعة الإعدادات
- إنشاء ملف installed.lock
- عرض معلومات تسجيل الدخول

---

## 🔐 بيانات الدخول الافتراضية

بعد إكمال التثبيت، استخدم البيانات التي أدخلتها في الخطوة 4.

**رابط لوحة التحكم:**
```
http://yourdomain.com/admin/
```

---

## 🧪 اختبار النظام

### 1. اختبار الواجهة الأمامية
```
http://yourdomain.com/
```

### 2. اختبار لوحة التحكم
```
http://yourdomain.com/admin/
```

### 3. اختبار تسجيل الدخول
- جرب تسجيل الدخول بحساب المدير
- تأكد من ظهور لوحة التحكم

### 4. اختبار اللغات
- انتقل بين العربية والإنجليزية
- تأكد من تبديل الاتجاه (RTL/LTR)

---

## 🐛 حل المشاكل الشائعة

### 1. شاشة بيضاء

**الحل:**
```bash
# تفعيل عرض الأخطاء
nano .env
# غيّر APP_DEBUG=true

# أو تحقق من السجلات
tail -f storage/logs/error.log
```

### 2. خطأ في الاتصال بقاعدة البيانات

**الحل:**
```bash
# تحقق من معلومات الاتصال في .env
cat .env | grep DB_

# اختبر الاتصال يدوياً
mysql -u root -p -h localhost
```

### 3. خطأ 500

**الحل:**
```bash
# تحقق من صلاحيات المجلدات
chmod -R 777 storage/
chmod -R 777 public/uploads/

# تحقق من ملف .htaccess
ls -la .htaccess
```

### 4. الترجمات لا تعمل

**الحل:**
```bash
# تحقق من وجود البيانات
mysql -u root -p car_rental_db -e "SELECT COUNT(*) FROM translations;"

# أعد استيراد الترجمات
mysql -u root -p car_rental_db < database/seeds/translations.sql
```

### 5. فشل رفع الملفات

**الحل:**
```bash
# تحقق من الصلاحيات
ls -la public/uploads/

# ضبط الصلاحيات
chmod -R 777 public/uploads/

# تحقق من إعدادات PHP
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

---

## 📚 الموارد المفيدة

- 📖 [التوثيق الكامل](docs/README.md)
- 🔧 [دليل التثبيت المفصل](docs/INSTALLATION.md)
- 👨‍💻 [دليل المطور](docs/DEVELOPER_GUIDE.md)
- 👤 [دليل المستخدم](docs/USER_GUIDE.md)
- 🌐 [توثيق API](docs/API.md)
- 💾 [هيكل قاعدة البيانات](docs/DATABASE.md)

---

## 🆘 الدعم

إذا واجهت أي مشاكل:

1. 📧 **البريد الإلكتروني:** support@example.com
2. 🌐 **الموقع:** https://example.com
3. 📱 **واتساب:** +966XXXXXXXXX
4. 📚 **التوثيق:** راجع مجلد `/docs/`

---

## ✅ قائمة التحقق النهائية

قبل البدء بالإنتاج:

- [ ] جميع الإعدادات في .env صحيحة
- [ ] APP_DEBUG=false في الإنتاج
- [ ] تم تفعيل HTTPS
- [ ] تم حذف مجلد /install/
- [ ] تم ضبط Cron Jobs
- [ ] تم اختبار جميع الوظائف
- [ ] تم عمل نسخة احتياطية
- [ ] تم مراجعة إعدادات الأمان
- [ ] تم اختبار النظام على أجهزة مختلفة
- [ ] تم تدريب المستخدمين

---

**الإصدار:** 1.0.0  
**آخر تحديث:** 2024-01-01

🎉 **مبروك! نظامك جاهز الآن!** 🚗