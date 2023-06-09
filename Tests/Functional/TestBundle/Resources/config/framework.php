<?php

use Symfony\Component\HttpKernel\Kernel;

$config = [
    'form' => true,
    'router' => [
        'utf8' => true,
    ],
    'secret' => 'test',
    'session' => [
        'storage_id' => 'session.storage.mock_file',
    ],
    'test' => true,
    'validation' => [
        'enabled' => true,
        'enable_annotations' => true,
    ],
];

if (version_compare(Kernel::VERSION, '2.7', '>=')) {
    // The 'assets' configuration is only available for Symfony >= 2.7
    $config['assets'] = false;
}

$container->loadFromExtension('framework', $config);
