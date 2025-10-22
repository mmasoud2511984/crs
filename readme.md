# 🚗 Car Rental Management System
## نظام إدارة تأجير السيارات

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0%2B-orange)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Proprietary-red)](LICENSE)

---

## 📋 نظرة عامة | Overview

**Arabic:**
نظام شامل ومتكامل لإدارة تأجير السيارات يوفر جميع المزايا اللازمة لإدارة أسطول السيارات، العملاء، العقود، والمالية بشكل احترافي وآمن.

**English:**
A comprehensive car rental management system providing all necessary features to manage fleet, customers, contracts, and finances professionally and securely.

---

## ✨ المزايا الرئيسية | Key Features

### 🌐 النظام الأساسي | Core System
- ✅ نظام متعدد اللغات (العربية/الإنجليزية وقابل للتوسع)
- ✅ دعم الفروع المتعددة
- ✅ نظام صلاحيات متقدم (RBAC)
- ✅ المصادقة الثنائية (2FA)
- ✅ تسجيل الدخول عبر Google OAuth
- ✅ تصميم متجاوب (RTL/LTR)

### 🚗 إدارة السيارات | Car Management
- ✅ سجل كامل للسيارات
- ✅ صور متعددة لكل سيارة
- ✅ متابعة الصيانة الدورية
- ✅ إدارة المخالفات
- ✅ السيارات المميزة
- ✅ خيار التأجير مع سائق

### 📝 نظام الإيجار | Rental System
- ✅ الحجز الإلكتروني
- ✅ خيارات الاستلام والتوصيل
- ✅ تتبع الموقع الجغرافي (GPS)
- ✅ توليد عقود PDF
- ✅ متابعة الدفعات
- ✅ تمديد العقود
- ✅ تذكيرات واتساب تلقائية

### 👥 إدارة العملاء | Customer Management
- ✅ تسجيل العملاء
- ✅ التحقق من الوثائق
- ✅ نظام نقاط الولاء
- ✅ نظام التقييمات
- ✅ سجل الإيجارات
- ✅ إدارة الملف الشخصي

### 💰 النظام المالي | Financial System
- ✅ إدارة الصندوق/الخزينة
- ✅ تتبع المعاملات
- ✅ طرق دفع متعددة
- ✅ التقارير المالية

### 📊 لوحة التحكم | Dashboard
- ✅ إحصائيات شاملة
- ✅ إدارة المستخدمين
- ✅ إدارة الإعدادات
- ✅ نظام الإشعارات
- ✅ توليد التقارير
- ✅ النسخ الاحتياطي

---

## 🛠️ المتطلبات | Requirements

### Server Requirements
- **PHP**: 8.2 or higher
- **MySQL**: 8.0 or higher
- **Apache/Nginx**: with mod_rewrite enabled
- **Composer**: Latest version

### PHP Extensions Required
- PDO
- mysqli
- json
- mbstring
- curl
- gd
- zip
- fileinfo

### Recommended
- SSL Certificate (for production)
- Cron Jobs support
- Memory: 256MB+
- Disk Space: 1GB+

---

## 📥 التثبيت | Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd car-rental-system
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
```bash
cp .env.example .env
```
Edit `.env` file with your database credentials and settings.

### 4. Create Folder Structure
```bash
chmod +x create-structure.sh
./create-structure.sh
```

### 5. Set Permissions
```bash
chmod -R 755 storage/
chmod -R 755 public/uploads/
```

### 6. Database Setup
```bash
mysql -u root -p
```
```sql
CREATE DATABASE car_rental_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE car_rental_db;
SOURCE database/schema.sql;
SOURCE database/seeds/languages.sql;
```

### 7. Access Installation Wizard
Navigate to: `http://yourdomain.com/install/`

Follow the 6-step installation wizard to complete setup.

---

## 🔧 Configuration

### Database Configuration
Edit `config/database.php` or use `.env` file:
```env
DB_HOST=localhost
DB_DATABASE=car_rental_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### App Configuration
Edit `.env` file for app settings:
```env
APP_NAME="Car Rental System"
APP_URL=http://yourdomain.com
APP_TIMEZONE=Asia/Riyadh
```

---

## 📁 Structure

```
car-rental-system/
├── app/                 # Application code
│   ├── controllers/     # MVC Controllers
│   ├── models/          # Data models
│   └── views/           # View templates
├── config/              # Configuration files
├── core/                # Core system classes
├── public/              # Public assets
├── database/            # Database files
├── storage/             # Storage (logs, cache, backups)
├── services/            # External services
├── cron/                # Scheduled tasks
└── docs/                # Documentation
```

---

## 🔐 Security Features

1. **CSRF Protection** on all forms
2. **XSS Prevention** on all outputs
3. **SQL Injection** protection via prepared statements
4. **Password Security** with bcrypt (cost 12)
5. **Session Security** with httponly & secure flags
6. **Rate Limiting** on login attempts
7. **File Upload** validation
8. **Audit Logging** for sensitive operations
9. **Two-Factor Authentication** (2FA)
10. **IP Tracking** and monitoring

---

## 📚 Documentation

- **Installation Guide**: `/docs/INSTALLATION.md`
- **User Manual**: `/docs/USER_GUIDE.md`
- **Developer Guide**: `/docs/DEVELOPER_GUIDE.md`
- **API Documentation**: `/docs/API.md`
- **Database Schema**: `/docs/DATABASE.md`

---

## 🌐 Multi-Language Support

The system supports multiple languages out of the box:
- Arabic (العربية) - Default
- English - Available
- Expandable to any language

All text is stored in the `translations` table. No hardcoded text anywhere in the system.

---

## 🔄 Cron Jobs

Setup the following cron jobs for automated tasks:

```bash
# Send rental reminders (daily at 9 AM)
0 9 * * * php /path/to/cron/send-rental-reminders.php

# Process WhatsApp queue (every 5 minutes)
*/5 * * * * php /path/to/cron/process-whatsapp-queue.php

# Check maintenance due (daily at 8 AM)
0 8 * * * php /path/to/cron/check-maintenance-due.php

# Cleanup sessions (daily at 2 AM)
0 2 * * * php /path/to/cron/cleanup-sessions.php

# Database backup (daily at 3 AM)
0 3 * * * php /path/to/cron/backup-database.php
```

---

## 🧪 Testing

```bash
# Run PHPUnit tests
composer test

# Run PHPStan analysis
composer analyse
```

---

## 📝 License

This project is proprietary software. All rights reserved.

---

## 👥 Support

For support and inquiries:
- Email: support@carrental.com
- Phone: +966 XX XXX XXXX

---

## 🔄 Updates

### Version 1.0 (Current)
- Initial release
- Complete core features
- Multi-language support
- Mobile responsive design

---

## 🙏 Acknowledgments

- PHP Community
- Open Source Contributors
- All Libraries Used in This Project

---

## 📊 System Stats

- **Total Files**: ~170 PHP files
- **Database Tables**: 34 tables
- **Lines of Code**: TBD
- **Languages Supported**: 2 (expandable)

---

**Made with ❤️ in Saudi Arabia 🇸🇦**