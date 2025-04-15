<?php
require_once '../config/config.php';
require_once '../utils/helpers.php';
require_once '../db/database.php';
require_once '../classes/Font.php';
require_once '../classes/FontGroup.php';
require_once '../classes/FontRepository.php';
require_once '../classes/FontGroupRepository.php';
$fontGroupRepository = new FontGroupRepository();
if (isset($_GET['id']) && !empty($_GET['id'])) { $group = $fontGroupRepository->findById($_GET['id']); if (!$group) { jsonResponse(false, 'Font group not found'); } jsonResponse(true, '', [ 'font_groups' => [$group->toArray()] ]);
}
$groups = $fontGroupRepository->findAll();
$groupsArray = [];
foreach ($groups as $group) { $groupsArray[] = $group->toArray();
}
jsonResponse(true, '', [ 'font_groups' => $groupsArray
]);
