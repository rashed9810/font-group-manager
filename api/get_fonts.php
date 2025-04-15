<?php
require_once '../config/config.php';
require_once '../utils/helpers.php';
require_once '../db/database.php';
require_once '../classes/Font.php';
require_once '../classes/FontRepository.php';
$fontRepository = new FontRepository();
$fonts = $fontRepository->findAll();
$fontsArray = [];
foreach ($fonts as $font) { $fontsArray[] = $font->toArray();
}
jsonResponse(true, '', [ 'fonts' => $fontsArray
]);
