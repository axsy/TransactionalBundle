<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

// Let's use closure to keep global context clean
call_user_func(function() {
    if (!is_file($autoloader = __DIR__ . '/../../../../../autoload.php')) {
        throw new \Exception('Can\'t find autoload configuration. Did you install vendors?');
    }
    require_once $autoloader;

    // Register the most simple and unsafe callable since we sure we'll deal only with defined annotations
    AnnotationRegistry::registerLoader('class_exists');
});