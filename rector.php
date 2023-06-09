<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\PHPUnit\Rector\ClassMethod\AddDoesNotPerformAssertionToNonAssertingTestRector;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->importNames();

    $rectorConfig->paths([
        __DIR__ . '/BrowserKit',
        __DIR__ . '/Command',
        __DIR__ . '/Cryptography',
        __DIR__ . '/DependencyInjection',
        __DIR__ . '/Entity',
        __DIR__ . '/Exception',
        __DIR__ . '/Form',
        __DIR__ . '/Model',
        __DIR__ . '/Plugin',
        __DIR__ . '/PluginController',
        __DIR__ . '/Tests',
        __DIR__ . '/Util',
        __DIR__ . '/Validator',
    ]);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
        SymfonyLevelSetList::UP_TO_SYMFONY_54,
    ]);

    $rectorConfig->skip([
        ReadOnlyPropertyRector::class,
        AddDoesNotPerformAssertionToNonAssertingTestRector::class,
        AnnotationToAttributeRector::class,
        ClosureToArrowFunctionRector::class,
        TypedPropertyFromAssignsRector::class,
    ]);
};
