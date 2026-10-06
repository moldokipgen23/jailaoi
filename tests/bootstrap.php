<?php
$loader = require __DIR__ . '/../vendor/autoload.php';
$base = dirname(__DIR__);
foreach ($loader->getClassMap() as $class => $path) {
    if (str_starts_with($class, 'Tests\\')) {
        $candidate = $base . '/tests/' . str_replace('\\', '/', substr($class, 6)) . '.php';
        if (is_file($candidate)) $loader->addClassMap([$class => $candidate]);
    }
    if (str_starts_with($class, 'App\\')) {
        $candidate = $base . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($candidate)) $loader->addClassMap([$class => $candidate]);
    }
}
$loader->setPsr4('App\\', [$base . '/app']);
$loader->setPsr4('Tests\\', [$base . '/tests']);
