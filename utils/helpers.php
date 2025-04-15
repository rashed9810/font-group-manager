<?php
function jsonResponse($success, $message = '', $data = [])
{
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
function generateUniqueFilename($originalName)
{
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}
function extractFontName($filePath, $originalFileName = '')
{
    if (!empty($originalFileName)) {
        $fontName = pathinfo($originalFileName, PATHINFO_FILENAME);
        $fontName = str_replace(['_', '-'], ' ', $fontName);
        $fontName = ucwords($fontName);
        return $fontName;
    }
    try {
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            throw new Exception('Could not open font file');
        }
        $contents = fread($handle, filesize($filePath));
        fclose($handle);
        $nameOffset = strpos($contents, 'name');
        if ($nameOffset === false) {
            throw new Exception('Could not find name table');
        }
        $fontName = '';
        if (empty($fontName)) {
            $originalName = pathinfo($filePath, PATHINFO_FILENAME);
            $parts = explode('_', $originalName);
            if (count($parts) > 1 && is_numeric(end($parts))) {
                array_pop($parts);
            }
            $fontName = implode(' ', $parts);
            $fontName = str_replace(['_', '-'], ' ', $fontName);
            $fontName = ucwords($fontName);
            if (preg_match('/^[a-f0-9]+$/i', $fontName)) {
                static $fontCounter = 0;
                $fontCounter++;
                $fontName = "Font $fontCounter";
            }
        }
        return $fontName;
    } catch (Exception $error) {
        $originalName = pathinfo($filePath, PATHINFO_FILENAME);
        $parts = explode('_', $originalName);
        if (count($parts) > 1 && is_numeric(end($parts))) {
            array_pop($parts);
        }
        $fontName = implode(' ', $parts);
        $fontName = str_replace(['_', '-'], ' ', $fontName);
        $fontName = ucwords($fontName);
        if (preg_match('/^[a-f0-9]+$/i', $fontName)) {
            static $fontCounter = 0;
            $fontCounter++;
            $fontName = "Font $fontCounter";
        }
        return $fontName;
    }
}
