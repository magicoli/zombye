<?php
/**
 * Simple autoloader for Zombye plugin classes
 */

spl_autoload_register(function($class) {
    $prefix = 'Zombye\\';
    $base_dir = __DIR__ . '/classes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;

    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-' . strtolower($relative_class) . '.php';

    if (file_exists($file)) require $file;
});
