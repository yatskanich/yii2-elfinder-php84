<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/plugin',
        __DIR__ . '/views',
        __DIR__ . '/volume',
        __DIR__ . '/Assets.php',
        __DIR__ . '/AssetsCallBack.php',
        __DIR__ . '/BaseController.php',
        __DIR__ . '/ButtonWidget.php',
        __DIR__ . '/Controller.php',
        __DIR__ . '/ElFinder.php',
        __DIR__ . '/elFinderApi.php',
        __DIR__ . '/InputFile.php',
        __DIR__ . '/PathController.php',
        __DIR__ . '/PluginInterface.php',
    ])
    ->withPhpSets(php84: true)
    ->withPhpVersion(PhpVersion::PHP_84)
    ->withTypeCoverageLevel(1)
    ->withDeadCodeLevel(1)
    ->withCodeQualityLevel(1);
