<?php
/**
 * File: check-install.php
 * Path: /install/check-install.php
 * Purpose: Check installation files and configuration
 */
session_start();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>🔍 فحص التثبيت</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #2563eb; }
        .good { color: #10b981; font-weight: bold; }
        .bad { color: #ef4444; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: right; border: 1px solid #ddd; }
        th { background: #f3f4f6; }
        .btn { display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 3px; }
        .section { margin: 30px 0; padding: 20px; background: #f9fafb; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 فحص ملفات التثبيت</h1>
        
        <!-- فحص الملفات -->
        <div class="section">
            <h2>📂 الملفات المطلوبة</h2>
            <table>
                <tr>
                    <th>الملف</th>
                    <th>الحالة</th>
                    <th>الحجم</th>
                    <th>الأذونات</th>
                </tr>
                <?php
                $requiredFiles = [
                    'index.php' => 'الملف الرئيسي',
                    'steps/step0.php' => 'اختيار اللغة',
                    'steps/step1b.php' => 'إنشاء المجلدات',
                    'steps/step1.php' => 'فحص المتطلبات',
                    'steps/step2.php' => 'قاعدة البيانات',
                    'steps/step3.php' => 'معلومات الشركة',
                    'steps/step4.php' => 'حساب المدير',
                    'steps/step5.php' => 'إعدادات اللغة',
                    'steps/step6.php' => 'الإنهاء',
                ];
                
                foreach ($requiredFiles as $file => $desc) {
                    $fullPath = __DIR__ . '/' . $file;
                    $exists = file_exists($fullPath);
                    $size = $exists ? filesize($fullPath) : 0;
                    $perms = $exists ? substr(sprintf('%o', fileperms($fullPath)), -4) : 'N/A';
                    
                    echo '<tr>';
                    echo '<td><code>' . $file . '</code><br><small>' . $desc . '</small></td>';
                    echo '<td class="' . ($exists ? 'good' : 'bad') . '">' . ($exists ? '✓ موجود' : '✗ مفقود') . '</td>';
                    echo '<td>' . ($exists ? number_format($size) . ' bytes' : '-') . '</td>';
                    echo '<td>' . $perms . '</td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>

        <!-- فحص Session -->
        <div class="section">
            <h2>🗂️ Session</h2>
            <table>
                <tr>
                    <td><strong>Session Status:</strong></td>
                    <td class="<?php echo session_status() === PHP_SESSION_ACTIVE ? 'good' : 'bad'; ?>">
                        <?php 
                        echo session_status() === PHP_SESSION_ACTIVE ? '✓ ACTIVE' : '✗ NOT ACTIVE';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>installer_lang:</strong></td>
                    <td class="<?php echo isset($_SESSION['installer_lang']) ? 'good' : 'bad'; ?>">
                        <?php 
                        echo isset($_SESSION['installer_lang']) 
                            ? '✓ ' . $_SESSION['installer_lang'] 
                            : '✗ غير محفوظة';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>install_data:</strong></td>
                    <td>
                        <?php 
                        if (isset($_SESSION['install_data'])) {
                            echo '<pre style="text-align: left; direction: ltr;">' . 
                                 htmlspecialchars(print_r($_SESSION['install_data'], true)) . 
                                 '</pre>';
                        } else {
                            echo '<span class="bad">✗ فارغة</span>';
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- فحص step1b.php -->
        <div class="section">
            <h2>🔍 فحص محتوى step1b.php</h2>
            <?php
            $step1bPath = __DIR__ . '/steps/step1b.php';
            if (file_exists($step1bPath)) {
                $content = file_get_contents($step1bPath);
                
                // البحث عن الروابط
                preg_match_all('/href=["\']([^"\']*step=\d+[^"\']*)["\']/i', $content, $matches);
                
                echo '<p><strong>الروابط الموجودة في step1b.php:</strong></p>';
                echo '<ul>';
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $link) {
                        $isStep2 = strpos($link, 'step=2') !== false;
                        echo '<li class="' . ($isStep2 ? 'good' : 'bad') . '">';
                        echo '<code>' . htmlspecialchars($link) . '</code>';
                        if ($isStep2) {
                            echo ' ✓ صحيح';
                        }
                        echo '</li>';
                    }
                } else {
                    echo '<li class="bad">لم يتم العثور على روابط!</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="bad">✗ الملف غير موجود!</p>';
            }
            ?>
        </div>

        <!-- فحص index.php -->
        <div class="section">
            <h2>🔍 فحص index.php</h2>
            <?php
            $indexPath = __DIR__ . '/index.php';
            if (file_exists($indexPath)) {
                $content = file_get_contents($indexPath);
                
                // التحقق من allowedSteps
                $hasAllowedSteps = strpos($content, 'allowedSteps') !== false;
                $has1b = strpos($content, "'1b'") !== false || strpos($content, '"1b"') !== false;
                $hasStep2 = strpos($content, "'2'") !== false || strpos($content, '2') !== false;
                
                echo '<ul>';
                echo '<li class="' . ($hasAllowedSteps ? 'good' : 'bad') . '">';
                echo $hasAllowedSteps ? '✓' : '✗';
                echo ' يحتوي على allowedSteps</li>';
                
                echo '<li class="' . ($has1b ? 'good' : 'bad') . '">';
                echo $has1b ? '✓' : '✗';
                echo ' الخطوة 1b مسموح بها</li>';
                
                echo '<li class="' . ($hasStep2 ? 'good' : 'bad') . '">';
                echo $hasStep2 ? '✓' : '✗';
                echo ' الخطوة 2 مسموح بها</li>';
                echo '</ul>';
                
                // عرض جزء من allowedSteps
                if (preg_match('/\$allowedSteps\s*=\s*\[(.*?)\]/s', $content, $match)) {
                    echo '<p><strong>allowedSteps:</strong></p>';
                    echo '<pre style="background: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto;">';
                    echo htmlspecialchars($match[0]);
                    echo '</pre>';
                }
            }
            ?>
        </div>

        <!-- اختبار الروابط -->
        <div class="section">
            <h2>🧪 اختبار الروابط</h2>
            <p>اختبر كل خطوة مباشرة:</p>
            <?php
            $steps = [
                0 => 'اختيار اللغة',
                '1b' => 'إنشاء المجلدات',
                1 => 'فحص المتطلبات',
                2 => 'قاعدة البيانات',
                3 => 'معلومات الشركة',
                4 => 'حساب المدير',
                5 => 'إعدادات اللغة',
                6 => 'الإنهاء'
            ];
            
            foreach ($steps as $num => $name) {
                echo '<a href="?step=' . $num . '" class="btn" target="_blank">';
                echo 'خطوة ' . $num . ': ' . $name;
                echo '</a>';
            }
            ?>
        </div>

        <!-- معلومات PHP -->
        <div class="section">
            <h2>⚙️ معلومات PHP</h2>
            <table>
                <tr>
                    <td><strong>PHP Version:</strong></td>
                    <td><?php echo PHP_VERSION; ?></td>
                </tr>
                <tr>
                    <td><strong>Server:</strong></td>
                    <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
                </tr>
                <tr>
                    <td><strong>Document Root:</strong></td>
                    <td><code><?php echo $_SERVER['DOCUMENT_ROOT']; ?></code></td>
                </tr>
                <tr>
                    <td><strong>Script Path:</strong></td>
                    <td><code><?php echo __DIR__; ?></code></td>
                </tr>
            </table>
        </div>

        <!-- الإجراءات -->
        <div class="section">
            <h2>⚡ إجراءات</h2>
            <a href="index.php" class="btn">← العودة للمثبت</a>
            <a href="?clear_session=1" class="btn" style="background: #ef4444;">مسح Session</a>
            <a href="debug.php" class="btn" style="background: #f59e0b;">Debug كامل</a>
        </div>

        <?php
        if (isset($_GET['clear_session'])) {
            session_destroy();
            echo '<div style="padding: 15px; background: #fef2f2; border: 2px solid #ef4444; border-radius: 5px; margin-top: 20px;">';
            echo '<strong>✓ تم مسح Session</strong><br>';
            echo '<a href="check-install.php">أعد تحميل الصفحة</a>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>