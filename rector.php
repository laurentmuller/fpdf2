<?php

/*
 * This file is part of the FPDF2 package.
 *
 * (c) bibi.nu <bibi@bibi.nu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;

$paths = [
    __DIR__ . '/src',
    __DIR__ . '/tests',
    __DIR__ . '/rector.php',
];

$skips = [
    __DIR__ . '/tests/fonts',
    PreferPHPUnitThisCallRector::class,
    // no space before or after statements
    NewlineAfterStatementRector::class,
    NewlineBeforeNewAssignSetRector::class,
    // don't separate constants
    NewlineBetweenClassLikeStmtsRector::class,
    // don't rename exception
    CatchExceptionNameMatchingTypeRector::class,
];

$sets = [
    // global
    SetList::PHP_83,
    SetList::CODE_QUALITY,
    SetList::CODING_STYLE,
    SetList::DEAD_CODE,
    SetList::PRIVATIZATION,
    SetList::INSTANCEOF,
    SetList::TYPE_DECLARATION,

    // PHP-Unit
    PHPUnitSetList::PHPUNIT_120,
    PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    PHPUnitSetList::PHPUNIT_MOCK_TO_STUB,
    PHPUnitSetList::PHPUNIT_NARROW_ASSERTS,
];

return RectorConfig::configure()
    ->withCache(__DIR__ . '/cache/rector')
    ->withRootFiles()
    ->withPaths($paths)
    ->withSkip($skips)
    ->withSets($sets)
    ->withConfiguredRule(ClassPropertyAssignToConstructorPromotionRector::class, [
        'rename_property' => false,
    ]);
