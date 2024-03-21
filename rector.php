<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withImportNames(importShortClasses: false)
    ->withPHPStanConfigs([__DIR__ . '/phpstan.dist.neon'])
    ->withRootFiles()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPhpSets(php83: true)
    ->withAttributesSets(phpunit: true)
    ->withPreparedSets();
