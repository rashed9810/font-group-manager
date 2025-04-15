<?php
class FontGroupRepository
{
    private $db;
    private $fontRepository;
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->fontRepository = new FontRepository();
    }
    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM font_groups ORDER BY title ASC");
        $groups = [];
        while ($row = $stmt->fetch()) {
            $group = new FontGroup($row);
            $this->loadFontsForGroup($group);
            $groups[] = $group;
        }
        return $groups;
    }
    public function findById($id)
    {
        $stmt = $this->db->query("SELECT * FROM font_groups WHERE id = ?", [$id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $group = new FontGroup($row);
        $this->loadFontsForGroup($group);
        return $group;
    }
    private function loadFontsForGroup($group)
    {
        $stmt = $this->db->query("SELECT f.* FROM fonts f JOIN font_group_items fgi ON f.id = fgi.font_id WHERE fgi.group_id = ? ORDER BY f.name ASC", [$group->getId()]);
        $fonts = [];
        while ($row = $stmt->fetch()) {
            $fonts[] = new Font($row);
        }
        $group->setFonts($fonts);
        return $group;
    }
    public function save($group, $fontIds = [])
    {
        $this->db->beginTransaction();
        try {
            if ($group->getId()) {
                $this->db->query("UPDATE font_groups SET title = ? WHERE id = ?", [$group->getTitle(), $group->getId()]);
                $this->db->query("DELETE FROM font_group_items WHERE group_id = ?", [$group->getId()]);
            } else {
                $this->db->query("INSERT INTO font_groups (title) VALUES (?)", [$group->getTitle()]);
                $group->setId($this->db->lastInsertId());
            }
            if (!empty($fontIds)) {
                $values = [];
                $params = [];
                foreach ($fontIds as $fontId) {
                    $values[] = "(?, ?)";
                    $params[] = $group->getId();
                    $params[] = $fontId;
                }
                $valuesStr = implode(', ', $values);
                $this->db->query("INSERT INTO font_group_items (group_id, font_id) VALUES $valuesStr", $params);
            }
            $this->db->commit();
            $this->loadFontsForGroup($group);
            return $group;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    public function delete($id)
    {
        $group = $this->findById($id);
        if (!$group) {
            return false;
        }
        $this->db->query("DELETE FROM font_groups WHERE id = ?", [$id]);
        return true;
    }
}
