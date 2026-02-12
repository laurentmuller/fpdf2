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
    '@auto' => true,
    '@auto:risky' => true,
    '@PHP8x3Migration' => true,
    '@PHP8x3Migration:risky' => true,
    '@PHPUnit11x0Migration:risky' => true,

    // --------------------------------------------------------------
    //  Rules override
    // --------------------------------------------------------------
    'strict_param' => true,
    'php_unit_strict' => true,
    'no_useless_else' => true,
    'no_unused_imports' => true,
    'no_useless_return' => true,
    'strict_comparison' => true,
    'ordered_interfaces' => true,
    'final_internal_class' => true,
    'method_chaining_indentation' => true,
    'concat_space' => ['spacing' => 'one'],
    'list_syntax' => ['syntax' => 'short'],
    'array_syntax' => ['syntax' => 'short'],
    'ordered_class_elements' => ['sort_algorithm' => 'alpha'],
    'attribute_empty_parentheses' => ['use_parentheses' => false],
    'native_function_invocation' => ['include' => ['@internal', '@all', '@compiler_optimized']],
    'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
    'new_with_braces' => ['anonymous_class' => false, 'named_class' => true],
    'ordered_imports' => ['imports_order' => ['const', 'class', 'function']],
    'blank_line_before_statement' => ['statements' => ['declare', 'try', 'return']],
    'header_comment' => ['header' => $comment, 'location' => 'after_open'],
    'phpdoc_to_comment' => [
        'allow_before_return_statement' => true,
        'ignored_tags' => ['phpstan-var', 'phpstan-param', 'phpstan-ignore'],
    ],
    'phpdoc_line_span' => [
        'case' => 'single',
        'const' => 'single',
        'property' => 'single',
        'other' => 'single',
    ],
];

$paths = [
    __DIR__ . '/src',
    __DIR__ . '/tests',
];

$files = [
    __FILE__,
    __DIR__ . '/rector.php',
];

$exclude = [
    'font',
    'fonts',
    'Legacy',
    'resources',
];

$finder = Finder::create()
    ->in($paths)
    ->append($files)
    ->exclude($exclude);

$config = new Config();

return $config
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setCacheFile(__DIR__ . '/cache/php-cs-fixer/.php-cs-fixer.cache')
    ->setUnsupportedPhpVersionAllowed(false)
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules($rules);
