<?php
class FontRepository
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM fonts ORDER BY name ASC");
        $fonts = [];
        while ($row = $stmt->fetch()) {
            $fonts[] = new Font($row);
        }
        return $fonts;
    }
    public function findById($id)
    {
        $stmt = $this->db->query("SELECT * FROM fonts WHERE id = ?", [$id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        return new Font($row);
    }
    public function findByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->query("SELECT * FROM fonts WHERE id IN ($placeholders) ORDER BY name ASC", $ids);
        $fonts = [];
        while ($row = $stmt->fetch()) {
            $fonts[] = new Font($row);
        }
        return $fonts;
    }
    public function save($font)
    {
        if ($font->getId()) {
            $this->db->query("UPDATE fonts SET name = ?, file_path = ?, original_name = ? WHERE id = ?", [$font->getName(), $font->getFilePath(), $font->getOriginalName(), $font->getId()]);
        } else {
            $this->db->query("INSERT INTO fonts (name, file_path, original_name) VALUES (?, ?, ?)", [$font->getName(), $font->getFilePath(), $font->getOriginalName()]);
            $font->setId($this->db->lastInsertId());
        }
        return $font;
    }
    public function delete($id)
    {
        $font = $this->findById($id);
        if (!$font) {
            return false;
        }
        $filePath = BASE_PATH . '/' . $font->getFilePath();
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $this->db->query("DELETE FROM fonts WHERE id = ?", [$id]);
        return true;
    }
}
