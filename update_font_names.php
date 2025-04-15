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
$counter = 0;
foreach ($fonts as $font) {
    $filePath = BASE_PATH . '/' . $font->getFilePath();
    
    if (file_exists($filePath)) {
        // Get original name
        $originalName = $font->getName();
        
        // Generate a better name
        $newName = '';
        
        // Remove unique ID and timestamp from filename
        $parts = explode('_', $originalName);
        if (count($parts) > 1 && is_numeric(end($parts))) {
            array_pop($parts); // Remove timestamp
        }
        
        $newName = implode(' ', $parts);
        
        // Clean up the font name
        $newName = str_replace(['_', '-'], ' ', $newName);
        $newName = ucwords($newName);
        
        // If it's still a hash-like name, use a generic name with a number
        if (preg_match('/^[a-f0-9]+$/i', $newName)) {
            $counter++;
            $newName = "Custom Font $counter";
        }
        
        // Update font name
        $font->setName($newName);
        $fontRepository->save($font);
        
        echo "Updated font: $originalName -> $newName\n";
    } else {
        echo "Warning: Font file not found: " . $filePath . "\n";
    }
}

echo "Font name update completed.\n";
