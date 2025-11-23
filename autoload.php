<?php
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    $directories = [
        __DIR__ . '/classes/',
        __DIR__ . '/interfaces/', 
        __DIR__ . '/traits/',
        __DIR__ . '/exceptions/'
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    throw new Exception("No se pudo cargar la clase: $class");
});