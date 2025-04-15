<?php
class Database
{
    private static $instance = null;
    private $conn;
    private function __construct()
    {
        try {
            $dbDir = dirname(DB_PATH);
            if (!file_exists($dbDir)) {
                mkdir($dbDir, 0777, true);
            }
            $this->conn = new PDO("sqlite:" . DB_PATH, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]);
            $this->conn->exec('PRAGMA foreign_keys = ON');
            $this->createTables();
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    private function createTables()
    {
        $this->conn->exec(" CREATE TABLE IF NOT EXISTS `fonts` ( `id` INTEGER PRIMARY KEY AUTOINCREMENT, `name` TEXT NOT NULL, `file_path` TEXT NOT NULL, `original_name` TEXT NOT NULL, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ) ");
        $this->conn->exec(" CREATE TABLE IF NOT EXISTS `font_groups` ( `id` INTEGER PRIMARY KEY AUTOINCREMENT, `title` TEXT NOT NULL, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ) ");
        $this->conn->exec(" CREATE TABLE IF NOT EXISTS `font_group_items` ( `id` INTEGER PRIMARY KEY AUTOINCREMENT, `group_id` INTEGER NOT NULL, `font_id` INTEGER NOT NULL, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (`group_id`) REFERENCES `font_groups` (`id`) ON DELETE CASCADE, FOREIGN KEY (`font_id`) REFERENCES `fonts` (`id`) ON DELETE CASCADE ) ");
    }
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getConnection()
    {
        return $this->conn;
    }
    public function query($query, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }
    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }
    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }
    public function commit()
    {
        return $this->conn->commit();
    }
    public function rollback()
    {
        return $this->conn->rollBack();
    }
}
