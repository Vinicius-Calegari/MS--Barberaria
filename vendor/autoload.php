<?php
// autoload.php
// This file handles autoloading for the MS Barbearia project

// Ensure Composer's autoloader is loaded
require_once __DIR__ . '/vendor/autoload.php';

// Custom autoloader for project-specific classes (if needed)
spl_autoload_register(function ($className) {
    // Convert namespace separators to directory separators
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    
    // Define possible locations for your classes
    $locations = [
        __DIR__ . '/src/',          // Main source directory
        __DIR__ . '/models/',       // Model classes
        __DIR__ . '/controllers/',  // Controller classes
        __DIR__ . '/libs/'         // Library classes
    ];
    
    // Try to load the class from each location
    foreach ($locations as $location) {
        $file = $location . $classPath . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Helper function to load configuration files
function loadConfig($configName) {
    $configFile = __DIR__ . '/config/' . $configName . '.php';
    if (file_exists($configFile)) {
        return require $configFile;
    }
    return null;
}

// Error reporting setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone setting
date_default_timezone_set('America/Sao_Paulo');