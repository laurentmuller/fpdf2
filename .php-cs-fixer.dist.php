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

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$comment = <<<COMMENT
    This file is part of the 'fpdf' package.

    For the license information, please view the LICENSE
    file that was distributed with this source code.

    @author bibi.nu <bibi@bibi.nu>
    COMMENT;

$rules = [
    // --------------------------------------------------------------
    //  Rule sets
    // --------------------------------------------------------------
    '@Symfony' => true,
    '@Symfony:risky' => true,
    '@PHP82Migration' => true,
    '@PHP80Migration:risky' => true,
    '@DoctrineAnnotation' => true,
    '@PHPUnit100Migration:risky' => true,

    // --------------------------------------------------------------
    //  Rules override
    // --------------------------------------------------------------
    'strict_param' => true,
    'php_unit_strict' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'no_unused_imports' => true,
    'strict_comparison' => true,
    'ordered_interfaces' => true,
    'final_internal_class' => true,
    'method_chaining_indentation' => true,
    'concat_space' => ['spacing' => 'one'],
    'list_syntax' => ['syntax' => 'short'],
    'array_syntax' => ['syntax' => 'short'],
    'ordered_class_elements' => ['sort_algorithm' => 'alpha'],
    'phpdoc_to_comment' => ['allow_before_return_statement' => true],
    'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
    'native_function_invocation' => ['include' => ['@internal', 'all']],
    'new_with_braces' => ['anonymous_class' => true, 'named_class' => true],
    'ordered_imports' => ['imports_order' => ['const', 'class', 'function']],
    'blank_line_before_statement' => ['statements' => ['declare', 'try', 'return']],
    'header_comment' => ['header' => $comment, 'location' => 'after_open', 'separate' => 'bottom'],
];

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->append([
        __FILE__,
        __DIR__ . '/rector.php',
    ])
    ->notPath('tests/resources')
    ->notPath('font')
    ->notName('FPDF.php');

$config = new Config();

return $config
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setCacheFile(__DIR__ . '/cache/php-cs-fixer/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules($rules);
