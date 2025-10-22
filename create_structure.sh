#!/bin/bash

# ====================================
# سكريبت إنشاء هيكل مجلدات نظام تأجير السيارات
# Create Car Rental System Directory Structure
# ====================================

echo "🚀 بدء إنشاء هيكل المجلدات..."

# المجلد الرئيسي
mkdir -p car-rental-system
cd car-rental-system

# المجلدات الرئيسية
mkdir -p config
mkdir -p core
mkdir -p app/{controllers/{frontend,backend,api},models,views/{frontend,backend}}
mkdir -p public/{assets/{css/{frontend,backend,common},js/{frontend,backend,common},images,fonts,vendor},uploads/{cars,customers,payments,documents}}
mkdir -p database/{seeds}
mkdir -p storage/{logs,cache/{translations,views},backups,temp}
mkdir -p services
mkdir -p cron
mkdir -p install/{steps,assets/{css,js}}
mkdir -p docs

# Frontend Views
mkdir -p app/views/frontend/{layouts,home,cars,rental,auth,profile,pages,components}

# Backend Views
mkdir -p app/views/backend/{layouts,dashboard,cars,rentals,customers,users,branches,maintenance,violations,finance,reports,settings,languages,notifications,reviews,pages}

echo "✅ تم إنشاء المجلدات الرئيسية"

# إنشاء ملفات .gitkeep للمجلدات الفارغة
find . -type d -empty -exec touch {}/.gitkeep \;

echo "✅ تم إنشاء ملفات .gitkeep"

# إنشاء ملف .gitignore
cat > .gitignore << 'EOF'
# Environment
.env
.env.local

# Dependencies
/vendor/
composer.lock

# IDE
.vscode/
.idea/
*.sublime-*

# OS
.DS_Store
Thumbs.db

# Uploads
/public/uploads/*
!/public/uploads/.gitkeep

# Storage
/storage/logs/*
!/storage/logs/.gitkeep
/storage/cache/*
!/storage/cache/.gitkeep
/storage/backups/*
!/storage/backups/.gitkeep
/storage/temp/*
!/storage/temp/.gitkeep

# Installation
/install/

# Node modules (if using)
node_modules/
package-lock.json
EOF

echo "✅ تم إنشاء ملف .gitignore"

# تعيين الصلاحيات
chmod -R 755 .
chmod -R 777 storage/
chmod -R 777 public/uploads/

echo "✅ تم تعيين الصلاحيات"

echo ""
echo "🎉 تم إنشاء هيكل المجلدات بنجاح!"
echo ""
echo "📂 الهيكل:"
tree -L 3 -I 'vendor|node_modules' || ls -R
