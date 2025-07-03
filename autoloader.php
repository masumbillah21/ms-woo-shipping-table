<?php
spl_autoload_register(function ($class) {
    $prefix = 'MWST\\';
    $base_dir = plugin_dir_path(__FILE__);

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace namespace separators with directory separators in the relative class name,
    // append with .php
    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

    // Replace underscores with dashes only in the filename part (optional, if your files use dashes)
    // For example, convert 'Shipping_Method' => 'Shipping-Method.php'
    // Split path into directories + filename
    $path_parts = pathinfo($file);
    $filename = str_replace('_', '-', $path_parts['basename']);
    $file = $path_parts['dirname'] . DIRECTORY_SEPARATOR . $filename;

    // Finally, include the file if it exists
    if (file_exists($file)) {
        require_once $file;
    }
});
