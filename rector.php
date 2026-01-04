<?php

/*
 * This file is part of the 'fpdf' package.
 *
 * For the license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author bibi.nu <bibi@bibi.nu>
 */

declare(strict_types=1);

use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
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
    __DIR__ . '/src/font',
    __DIR__ . '/tests/fonts',
    __DIR__ . '/tests/Legacy',
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
    SetList::PHP_82,
    SetList::CODE_QUALITY,
    SetList::CODING_STYLE,
    SetList::PRIVATIZATION,
    SetList::INSTANCEOF,

    // PHP-Unit
    PHPUnitSetList::PHPUNIT_110,
    PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
];

$rules = [
    // static closure and arrow functions
    StaticClosureRector::class,
    StaticArrowFunctionRector::class,
];

return RectorConfig::configure()
    ->withCache(__DIR__ . '/cache/rector')
    ->withRootFiles()
    ->withPaths($paths)
    ->withSkip($skips)
    ->withSets($sets)
    ->withRules($rules)
    ->withConfiguredRule(ClassPropertyAssignToConstructorPromotionRector::class, [
        'rename_property' => false,
    ]);
