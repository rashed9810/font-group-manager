<?php
class Font
{
    private $id;
    private $name;
    private $filePath;
    private $originalName;
    private $createdAt;
    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->filePath = $data['file_path'] ?? '';
        $this->originalName = $data['original_name'] ?? '';
        $this->createdAt = $data['created_at'] ?? null;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function getFilePath()
    {
        return $this->filePath;
    }
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }
    public function getOriginalName()
    {
        return $this->originalName;
    }
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;
        return $this;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function toArray()
    {
        return ['id' => $this->id, 'name' => $this->name, 'file_path' => $this->filePath, 'original_name' => $this->originalName, 'created_at' => $this->createdAt];
    }
}
