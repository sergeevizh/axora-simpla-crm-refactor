<?php

$finder = PhpCsFixer\Finder::create()
->exclude('tests/Fixtures')
->in(__DIR__)
->append([
__DIR__.'/dev-tools/doc.php',
// __DIR__.'/php-cs-fixer', disabled, as we want to be able to run bootstrap file even on lower PHP version, to show nice message
]);

$config = new PhpCsFixer\Config();
$config
->setRiskyAllowed(true)
->setRules([
    '@PSR2' => true,
    'array_syntax' => ['syntax' => 'short'],
    'blank_line_after_namespace' => true,
    'blank_line_before_statement' => true,
    'linebreak_after_opening_tag' => true,
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'no_extra_blank_lines' => true,
    'no_trailing_whitespace' => true,
    'no_unused_imports' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'phpdoc_order' => true,
    'phpdoc_separation' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_trim' => true,
    'single_blank_line_at_eof' => true,
    'single_blank_line_before_namespace' => true,
])
->setFinder($finder)
;


return $config;