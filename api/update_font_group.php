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
if (!isset($_POST['id']) || empty($_POST['id'])) { jsonResponse(false, 'Group ID is required');
}
if (!isset($_POST['title']) || empty($_POST['title'])) { jsonResponse(false, 'Group title is required');
}
if (!isset($_POST['font_data']) || empty($_POST['font_data'])) { jsonResponse(false, 'Font data is required');
}
try { $fontData = json_decode($_POST['font_data'], true); if (!is_array($fontData) || count($fontData) < 2) { throw new Exception('At least 2 fonts are required'); } $fontIds = array_map(function($item) { return $item['id']; }, $fontData);
} catch (Exception $e) { jsonResponse(false, $e->getMessage());
}
$fontGroupRepository = new FontGroupRepository();
$group = $fontGroupRepository->findById($_POST['id']);
if (!$group) { jsonResponse(false, 'Font group not found');
}
$group->setTitle(sanitizeInput($_POST['title']));
try { $group = $fontGroupRepository->save($group, $fontIds);
} catch (Exception $e) { jsonResponse(false, 'Failed to update font group: ' . $e->getMessage());
}
jsonResponse(true, 'Font group updated successfully', [ 'group' => $group->toArray()
]);
