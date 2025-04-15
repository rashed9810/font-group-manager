<?php
require_once '../config/config.php';
require_once '../utils/helpers.php';
require_once '../db/database.php';
require_once '../classes/Font.php';
require_once '../classes/FontGroup.php';
require_once '../classes/FontRepository.php';
require_once '../classes/FontGroupRepository.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { jsonResponse(false, 'Invalid request method');
}
if (!isset($_POST['group_id']) || empty($_POST['group_id'])) { jsonResponse(false, 'Group ID is required');
}
$fontGroupRepository = new FontGroupRepository();
$success = $fontGroupRepository->delete($_POST['group_id']);
if (!$success) { jsonResponse(false, 'Failed to delete font group');
}
jsonResponse(true, 'Font group deleted successfully');
