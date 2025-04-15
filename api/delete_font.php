<?php
require_once '../config/config.php';
require_once '../utils/helpers.php';
require_once '../db/database.php';
require_once '../classes/Font.php';
require_once '../classes/FontRepository.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { jsonResponse(false, 'Invalid request method');
}
if (!isset($_POST['font_id']) || empty($_POST['font_id'])) { jsonResponse(false, 'Font ID is required');
}
$fontRepository = new FontRepository();
$success = $fontRepository->delete($_POST['font_id']);
if (!$success) { jsonResponse(false, 'Failed to delete font');
}
jsonResponse(true, 'Font deleted successfully');
