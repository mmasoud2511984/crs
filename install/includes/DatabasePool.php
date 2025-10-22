<?php
/**
 * DatabasePool Class
 * Path: /install/includes/DatabasePool.php
 * Purpose: Manage database pool allocation and reservations
 */

class DatabasePool
{
    private $configFile;
    private $data;

    public function __construct($configFile = null)
    {
        $this->configFile = $configFile ?: __DIR__ . '/../config/databases.json';
        $this->loadData();
    }

    /**
     * تحميل البيانات من ملف JSON
     */
    private function loadData()
    {
        if (!file_exists($this->configFile)) {
            throw new Exception('ملف قواعد البيانات غير موجود: ' . $this->configFile);
        }

        $content = file_get_contents($this->configFile);
        $this->data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('خطأ في قراءة ملف القواعد: ' . json_last_error_msg());
        }

        if (!isset($this->data['databases']) || !is_array($this->data['databases'])) {
            throw new Exception('صيغة ملف القواعد غير صحيحة');
        }
    }

    /**
     * حفظ البيانات إلى الملف مع حماية من التعارضات
     */
    private function saveData()
    {
        $content = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        $fp = fopen($this->configFile, 'w');
        if ($fp === false) {
            throw new Exception('لا يمكن فتح ملف القواعد للكتابة');
        }
        
        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $content);
            fflush($fp);
            flock($fp, LOCK_UN);
        } else {
            fclose($fp);
            throw new Exception('لا يمكن قفل ملف القواعد');
        }
        
        fclose($fp);
    }

    /**
     * الحصول على قاعدة بيانات متاحة
     */
    public function getAvailableDatabase()
    {
        foreach ($this->data['databases'] as $index => $db) {
            if ($db['status'] === 'available') {
                // حجز قاعدة البيانات
                $this->data['databases'][$index]['status'] = 'reserved';
                $this->data['databases'][$index]['reserved_at'] = date('Y-m-d H:i:s');
                $this->data['databases'][$index]['reserved_by'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                
                $this->saveData();
                
                return [
                    'id' => $db['id'],
                    'host' => $this->data['settings']['host'],
                    'port' => $this->data['settings']['port'],
                    'prefix' => $db['prefix'],
                    'dbname' => $db['dbname'],
                    'full_dbname' => $db['prefix'] . $db['dbname'],
                    'username' => $db['username'],
                    'full_username' => $db['prefix'] . $db['username'],
                    'password' => $db['password']
                ];
            }
        }

        return null; // لا توجد قواعد متاحة
    }

    /**
     * تأكيد استخدام قاعدة البيانات
     */
    public function confirmDatabase($id)
    {
        foreach ($this->data['databases'] as $index => $db) {
            if ($db['id'] == $id) {
                $this->data['databases'][$index]['status'] = 'used';
                $this->data['databases'][$index]['confirmed_at'] = date('Y-m-d H:i:s');
                $this->saveData();
                return true;
            }
        }
        return false;
    }

    /**
     * إلغاء حجز قاعدة البيانات (في حالة فشل التثبيت)
     */
    public function releaseDatabase($id)
    {
        foreach ($this->data['databases'] as $index => $db) {
            if ($db['id'] == $id && $db['status'] === 'reserved') {
                $this->data['databases'][$index]['status'] = 'available';
                $this->data['databases'][$index]['reserved_at'] = null;
                $this->data['databases'][$index]['reserved_by'] = null;
                $this->saveData();
                return true;
            }
        }
        return false;
    }

    /**
     * إحصائيات القواعد
     */
    public function getStats()
    {
        $stats = [
            'total' => count($this->data['databases']),
            'available' => 0,
            'reserved' => 0,
            'used' => 0
        ];

        foreach ($this->data['databases'] as $db) {
            if (isset($stats[$db['status']])) {
                $stats[$db['status']]++;
            }
        }

        return $stats;
    }

    /**
     * إلغاء حجز القواعد القديمة (تنظيف تلقائي)
     */
    public function cleanupOldReservations()
    {
        $hours = $this->data['settings']['auto_release_after_hours'] ?? 24;
        $cutoff = date('Y-m-d H:i:s', strtotime("-$hours hours"));
        
        $cleaned = 0;
        foreach ($this->data['databases'] as $index => $db) {
            if ($db['status'] === 'reserved' && 
                isset($db['reserved_at']) && 
                $db['reserved_at'] && 
                $db['reserved_at'] < $cutoff) {
                
                $this->data['databases'][$index]['status'] = 'available';
                $this->data['databases'][$index]['reserved_at'] = null;
                $this->data['databases'][$index]['reserved_by'] = null;
                $cleaned++;
            }
        }

        if ($cleaned > 0) {
            $this->saveData();
        }

        return $cleaned;
    }

    /**
     * اختبار الاتصال بقاعدة بيانات
     */
    public function testConnection($dbInfo)
    {
        try {
            $dsn = "mysql:host={$dbInfo['host']};port={$dbInfo['port']};dbname={$dbInfo['full_dbname']};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbInfo['full_username'], $dbInfo['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // التحقق من عدد الجداول
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            return [
                'success' => true,
                'is_empty' => count($tables) === 0,
                'table_count' => count($tables)
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * الحصول على معلومات قاعدة بيانات محددة
     */
    public function getDatabaseById($id)
    {
        foreach ($this->data['databases'] as $db) {
            if ($db['id'] == $id) {
                return [
                    'id' => $db['id'],
                    'host' => $this->data['settings']['host'],
                    'port' => $this->data['settings']['port'],
                    'prefix' => $db['prefix'],
                    'dbname' => $db['dbname'],
                    'full_dbname' => $db['prefix'] . $db['dbname'],
                    'username' => $db['username'],
                    'full_username' => $db['prefix'] . $db['username'],
                    'password' => $db['password'],
                    'status' => $db['status']
                ];
            }
        }
        return null;
    }
}