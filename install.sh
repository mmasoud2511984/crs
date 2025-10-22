#!/bin/bash

# ====================================
# سكريبت التثبيت الكامل لنظام تأجير السيارات
# Car Rental System Complete Installation Script
# ====================================

echo "🚗 مرحباً بك في نظام إدارة تأجير السيارات"
echo "======================================"
echo ""

# التحقق من الصلاحيات
if [ "$EUID" -ne 0 ]; then 
    echo "⚠️  تحذير: يُفضل تشغيل السكريبت بصلاحيات root"
    echo "للمتابعة بدون صلاحيات root، اضغط Enter"
    read
fi

# الألوان للتنسيق
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# دالة للطباعة الملونة
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# دالة للتحقق من تثبيت البرامج
check_command() {
    if command -v $1 &> /dev/null; then
        print_success "$1 مثبت"
        return 0
    else
        print_error "$1 غير مثبت"
        return 1
    fi
}

echo "📋 الخطوة 1: التحقق من المتطلبات"
echo "======================================"

# التحقق من PHP
if check_command php; then
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    echo "   إصدار PHP: $PHP_VERSION"
    if php -r "exit(version_compare(PHP_VERSION, '8.2.0', '>=') ? 0 : 1);"; then
        print_success "إصدار PHP مناسب (8.2+)"
    else
        print_error "يجب تحديث PHP إلى 8.2 أو أحدث"
        exit 1
    fi
else
    print_error "PHP غير مثبت!"
    exit 1
fi

# التحقق من Composer
if ! check_command composer; then
    print_warning "Composer غير مثبت. جاري التثبيت..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    print_success "تم تثبيت Composer"
fi

# التحقق من MySQL
if check_command mysql; then
    MYSQL_VERSION=$(mysql --version | awk '{print $5}' | cut -d',' -f1)
    echo "   إصدار MySQL: $MYSQL_VERSION"
else
    print_error "MySQL غير مثبت!"
    exit 1
fi

# التحقق من Git
check_command git

echo ""
echo "📁 الخطوة 2: إنشاء هيكل المجلدات"
echo "======================================"

# إنشاء المجلدات
mkdir -p config core app/{controllers/{frontend,backend,api},models,views/{frontend,backend}}
mkdir -p public/{assets/{css/{frontend,backend,common},js/{frontend,backend,common},images,fonts,vendor},uploads/{cars,customers,payments,documents}}
mkdir -p database/seeds storage/{logs,cache/{translations,views},backups,temp}
mkdir -p services cron install/{steps,assets/{css,js}} docs

# إنشاء مجلدات Views الفرعية
mkdir -p app/views/frontend/{layouts,home,cars,rental,auth,profile,pages,components}
mkdir -p app/views/backend/{layouts,dashboard,cars,rentals,customers,users,branches,maintenance,violations,finance,reports,settings,languages,notifications,reviews,pages}

print_success "تم إنشاء هيكل المجلدات"

# إنشاء ملفات .gitkeep
find . -type d -empty -exec touch {}/.gitkeep \;
print_success "تم إنشاء ملفات .gitkeep"

echo ""
echo "⚙️  الخطوة 3: إعداد الملفات الأساسية"
echo "======================================"

# إنشاء .gitignore
cat > .gitignore << 'EOF'
.env
.env.local
/vendor/
composer.lock
.vscode/
.idea/
*.sublime-*
.DS_Store
Thumbs.db
/public/uploads/*
!/public/uploads/.gitkeep
/storage/logs/*
!/storage/logs/.gitkeep
/storage/cache/*
!/storage/cache/.gitkeep
/storage/backups/*
!/storage/backups/.gitkeep
/storage/temp/*
!/storage/temp/.gitkeep
/install/
node_modules/
package-lock.json
EOF
print_success "تم إنشاء .gitignore"

# نسخ .env.example إلى .env
if [ ! -f .env ]; then
    cp .env.example .env
    print_success "تم إنشاء ملف .env"
else
    print_warning "ملف .env موجود مسبقاً"
fi

echo ""
echo "📦 الخطوة 4: تثبيت الاعتماديات"
echo "======================================"

# تثبيت Composer packages
print_warning "جاري تثبيت Composer packages..."
composer install --no-interaction --prefer-dist --optimize-autoloader

if [ $? -eq 0 ]; then
    print_success "تم تثبيت Composer packages بنجاح"
else
    print_error "فشل تثبيت Composer packages"
    exit 1
fi

echo ""
echo "🗄️  الخطوة 5: إعداد قاعدة البيانات"
echo "======================================"

# طلب معلومات قاعدة البيانات
echo "الرجاء إدخال معلومات قاعدة البيانات:"
read -p "اسم المضيف (localhost): " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "اسم المستخدم (root): " DB_USER
DB_USER=${DB_USER:-root}

read -sp "كلمة المرور: " DB_PASS
echo ""

read -p "اسم قاعدة البيانات (car_rental_db): " DB_NAME
DB_NAME=${DB_NAME:-car_rental_db}

# التحقق من الاتصال بقاعدة البيانات
echo "🔍 جاري التحقق من الاتصال..."
if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e ";" 2>/dev/null; then
    print_success "تم الاتصال بقاعدة البيانات بنجاح"
else
    print_error "فشل الاتصال بقاعدة البيانات. تحقق من المعلومات"
    exit 1
fi

# إنشاء قاعدة البيانات
echo "📊 جاري إنشاء قاعدة البيانات..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [ $? -eq 0 ]; then
    print_success "تم إنشاء قاعدة البيانات: $DB_NAME"
else
    print_error "فشل إنشاء قاعدة البيانات"
    exit 1
fi

# استيراد الهيكل
if [ -f "database/schema.sql" ]; then
    echo "📋 جاري استيراد هيكل قاعدة البيانات..."
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/schema.sql
    print_success "تم استيراد هيكل قاعدة البيانات"
else
    print_warning "ملف schema.sql غير موجود"
fi

# استيراد البيانات الأولية
echo "🌱 جاري استيراد البيانات الأولية..."

for seed_file in database/seeds/*.sql; do
    if [ -f "$seed_file" ]; then
        filename=$(basename "$seed_file")
        echo "   جاري استيراد: $filename"
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$seed_file"
        print_success "تم استيراد: $filename"
    fi
done

# تحديث ملف .env
echo ""
echo "📝 جاري تحديث ملف .env"
sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
print_success "تم تحديث إعدادات قاعدة البيانات في .env"

echo ""
echo "🔐 الخطوة 6: إعداد الأمان"
echo "======================================"

# توليد مفتاح التشفير
ENCRYPTION_KEY=$(openssl rand -base64 32)
sed -i "s/ENCRYPTION_KEY=.*/ENCRYPTION_KEY=$ENCRYPTION_KEY/" .env
print_success "تم توليد مفتاح التشفير"

echo ""
echo "📂 الخطوة 7: ضبط الصلاحيات"
echo "======================================"

# ضبط صلاحيات المجلدات
chmod -R 755 .
chmod -R 777 storage/
chmod -R 777 public/uploads/

print_success "تم ضبط صلاحيات المجلدات"

echo ""
echo "🎨 الخطوة 8: التحقق من الإعداد"
echo "======================================"

# التحقق من امتدادات PHP المطلوبة
echo "📋 فحص امتدادات PHP:"
php_extensions=("pdo" "pdo_mysql" "mbstring" "json" "curl" "gd" "zip" "openssl")

for ext in "${php_extensions[@]}"; do
    if php -m | grep -q "^$ext$"; then
        print_success "امتداد $ext متوفر"
    else
        print_error "امتداد $ext غير متوفر"
    fi
done

echo ""
echo "✅ الخطوة 9: اكتمال التثبيت"
echo "======================================"

# إنشاء ملف installed.lock
touch config/installed.lock
print_success "تم إنشاء ملف installed.lock"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🎉 تم التثبيت بنجاح!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📌 الخطوات التالية:"
echo ""
echo "1. ⚙️  أكمل الإعداد من خلال معالج التثبيت:"
echo "   👉 http://yourdomain.com/install/"
echo ""
echo "2. 🔒 للأمان، احذف مجلد /install/ بعد انتهاء التثبيت"
echo ""
echo "3. 📚 راجع التوثيق في مجلد /docs/"
echo ""
echo "4. ⏰ أعد إعداد Cron Jobs للمهام المجدولة:"
echo "   crontab -e"
echo ""
echo "   # إضف السطور التالية:"
echo "   0 * * * * php /path/to/cron/send-rental-reminders.php"
echo "   0 8 * * * php /path/to/cron/check-maintenance-due.php"
echo "   */5 * * * * php /path/to/cron/process-whatsapp-queue.php"
echo "   0 2 * * * php /path/to/cron/backup-database.php"
echo "   0 3 * * * php /path/to/cron/cleanup-sessions.php"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📞 للدعم:"
echo "   📧 Email: support@example.com"
echo "   🌐 Website: https://example.com"
echo ""
echo "شكراً لاستخدامك نظام إدارة تأجير السيارات! 🚗"
echo ""