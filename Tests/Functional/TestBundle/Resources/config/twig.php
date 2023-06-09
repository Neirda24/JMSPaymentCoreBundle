<?php

$config = [
    'paths' => [
        __DIR__ . '/../../Resources/views' => 'TestBundle',
    ],
];

$container->loadFromExtension('twig', $config);
