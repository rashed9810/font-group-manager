<?php
/**
 * Font Group System - Installation Script
 */

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die('PHP 7.4 or higher is required. Your PHP version: ' . PHP_VERSION);
}

// Check required extensions
$requiredExtensions = ['mysqli', 'json', 'fileinfo'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

if (!empty($missingExtensions)) {
    die('Missing required PHP extensions: ' . implode(', ', $missingExtensions));
}

// Check if config file exists
if (!file_exists('config/config.php')) {
    die('Configuration file not found. Please create config/config.php based on config/config.sample.php');
}

// Include config
require_once 'config/config.php';

// Check database connection
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($db->connect_error) {
        throw new Exception('Database connection failed: ' . $db->connect_error);
    }
    
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    die('Database connection error: ' . $e->getMessage());
}

// Check if tables exist
$tables = ['fonts', 'font_groups', 'font_group_fonts'];
$missingTables = [];

foreach ($tables as $table) {
    $result = $db->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows === 0) {
        $missingTables[] = $table;
    }
}

if (!empty($missingTables)) {
    echo "! Missing database tables: " . implode(', ', $missingTables) . "\n";
    echo "  Would you like to create these tables now? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    
    if (strtolower($line) === 'y') {
        echo "Creating tables...\n";
        
        // Read SQL file
        $sql = file_get_contents('db/schema.sql');
        
        // Execute SQL
        if ($db->multi_query($sql)) {
            do {
                // Store result
                if ($result = $db->store_result()) {
                    $result->free();
                }
            } while ($db->more_results() && $db->next_result());
            
            echo "✓ Tables created successfully\n";
        } else {
            echo "! Error creating tables: " . $db->error . "\n";
        }
    }
}

// Check uploads directory
$uploadsDir = 'uploads/fonts';
if (!is_dir($uploadsDir)) {
    echo "! Uploads directory not found. Creating...\n";
    mkdir($uploadsDir, 0755, true);
    echo "✓ Uploads directory created\n";
}

// Check if uploads directory is writable
if (!is_writable($uploadsDir)) {
    echo "! Uploads directory is not writable. Please set proper permissions.\n";
    echo "  On Linux/Mac: chmod 755 $uploadsDir\n";
    echo "  On Windows: Set write permissions for the web server user\n";
} else {
    echo "✓ Uploads directory is writable\n";
}

// All checks passed
echo "\n✓ Installation checks completed successfully!\n";
echo "You can now access the application at: http://your-server/path-to-app/\n";
