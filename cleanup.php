<?php
// Define directories to clean up
$directories = [
    'api',
    'assets/js',
    'assets/css',
    'classes',
    'config',
    'db',
    'utils'
];

// Define file extensions to process
$extensions = ['php', 'js', 'css'];

// Define patterns to remove
$patterns = [
    // Remove multi-line comments
    '/\/\*\*[\s\S]*?\*\/\s*/',
    // Remove single-line comments (but keep license comments)
    '/(?<!:)\/\/(?!.*license).*/',
    // Remove empty lines
    '/^\s*[\r\n]+/m',
    // Remove multiple empty lines
    '/[\r\n]{3,}/s'
];

// Function to clean up a file
function cleanupFile($filePath) {
    echo "Cleaning up: $filePath\n";
    
    // Read file content
    $content = file_get_contents($filePath);
    
    // Skip if file is empty
    if (empty($content)) {
        echo "  - File is empty, skipping\n";
        return;
    }
    
    // Save original size
    $originalSize = strlen($content);
    
    // Apply cleanup patterns
    global $patterns;
    foreach ($patterns as $pattern) {
        $content = preg_replace($pattern, '', $content);
    }
    
    // Remove multiple spaces
    $content = preg_replace('/\s{2,}/', ' ', $content);
    
    // Calculate size reduction
    $newSize = strlen($content);
    $reduction = $originalSize - $newSize;
    $percentReduction = ($reduction / $originalSize) * 100;
    
    echo "  - Reduced by " . number_format($percentReduction, 2) . "% (" . 
         number_format($reduction / 1024, 2) . " KB)\n";
    
    // Write cleaned content back to file
    file_put_contents($filePath, $content);
}

// Process directories
foreach ($directories as $dir) {
    echo "\nProcessing directory: $dir\n";
    
    // Get all files in directory
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );
    
    // Process each file
    foreach ($files as $file) {
        // Skip directories
        if ($file->isDir()) {
            continue;
        }
        
        // Get file extension
        $extension = pathinfo($file->getPathname(), PATHINFO_EXTENSION);
        
        // Process only specified extensions
        if (in_array($extension, $extensions)) {
            cleanupFile($file->getPathname());
        }
    }
}

echo "\nCleanup completed!\n";
