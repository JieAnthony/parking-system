<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

return (new MattAllan\LaravelCodeStyle\Config())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(app_path())
            ->in(config_path())
            ->in(base_path('routes'))
            ->in(base_path('tests'))
    )
    ->setRules([
        '@Laravel' => true,
    ])
    ->setUsingCache(false);
