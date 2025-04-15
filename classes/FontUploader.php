<?php
class FontUploader
{
    private $fontRepository;
    public function __construct()
    {
        $this->fontRepository = new FontRepository();
    }
    public function upload($file)
    {
        if (!$this->isValidFile($file)) {
            return false;
        }
        $filename = generateUniqueFilename($file['name']);
        $uploadPath = UPLOAD_DIR . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return false;
        }
        $fontName = $this->extractFontName($file['name'], $uploadPath);
        $font = new Font(['name' => $fontName, 'file_path' => FONT_URL . '/' . $filename, 'original_name' => $file['name']]);
        return $this->fontRepository->save($font);
    }
    private function isValidFile($file)
    {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return false;
        }
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension'] ?? '');
        if ($extension !== 'ttf') {
            return false;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }
        return true;
    }
    private function extractFontName($fileName, $filePath)
    {
        return extractFontName($filePath, $fileName);
    }
}
