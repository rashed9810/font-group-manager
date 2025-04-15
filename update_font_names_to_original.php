<?php
require_once 'config/config.php';
require_once 'utils/helpers.php';
require_once 'db/database.php';
require_once 'classes/Font.php';
require_once 'classes/FontRepository.php';

echo "Starting font name update process...\n";

// Get font repository
$fontRepository = new FontRepository();

// Get all fonts
$fonts = $fontRepository->findAll();

echo "Found " . count($fonts) . " fonts to update.\n";

// Update each font
foreach ($fonts as $font) {
    // Get original name
    $originalName = $font->getOriginalName();
    
    if (!empty($originalName)) {
        // Extract font name from original name
        $newName = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Clean up the font name
        $newName = str_replace(['_', '-'], ' ', $newName);
        $newName = ucwords($newName);
        
        // Update font name
        $font->setName($newName);
        $fontRepository->save($font);
        
        echo "Updated font: " . $font->getName() . " -> $newName\n";
    } else {
        echo "Warning: No original name for font ID " . $font->getId() . "\n";
    }
}

echo "Font name update completed.\n";
