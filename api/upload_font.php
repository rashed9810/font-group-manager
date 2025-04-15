<?php
require_once '../config/config.php';
require_once '../utils/helpers.php';
require_once '../db/database.php';
require_once '../classes/Font.php';
require_once '../classes/FontRepository.php';
require_once '../classes/FontUploader.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { jsonResponse(false, 'Invalid request method');
}
if (!isset($_FILES['font_file']) || empty($_FILES['font_file']['name'])) { jsonResponse(false, 'No file uploaded');
}
$uploader = new FontUploader();
$font = $uploader->upload($_FILES['font_file']);
if (!$font) { jsonResponse(false, 'Failed to upload font. Make sure it is a valid TTF file.');
}
jsonResponse(true, 'Font uploaded successfully', [ 'font' => $font->toArray()
]);
