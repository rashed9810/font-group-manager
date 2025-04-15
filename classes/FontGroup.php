<?php
class FontGroup
{
    private $id;
    private $title;
    private $fonts = [];
    private $createdAt;
    private $updatedAt;
    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->fonts = $data['fonts'] ?? [];
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
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
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    public function getFonts()
    {
        return $this->fonts;
    }
    public function setFonts($fonts)
    {
        $this->fonts = $fonts;
        return $this;
    }
    public function addFont($font)
    {
        $this->fonts[] = $font;
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    public function toArray()
    {
        $fontsArray = [];
        foreach ($this->fonts as $font) {
            if ($font instanceof Font) {
                $fontsArray[] = $font->toArray();
            } else {
                $fontsArray[] = $font;
            }
        }
        return ['id' => $this->id, 'title' => $this->title, 'fonts' => $fontsArray, 'created_at' => $this->createdAt, 'updated_at' => $this->updatedAt];
    }
}
