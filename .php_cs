<?php
declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->in(__DIR__.'/bin')
;

return PhpCsFixer\Config::create()
        ->setRiskyAllowed(true)
        ->setRules([
            '@Symfony' => true,
            '@Symfony:risky' => true,
            'ordered_imports' => true,
            'declare_strict_types' => true,
            'strict_param' => true,
            'strict_comparison' => true,
            'array_syntax' => ['syntax' => 'short'],
            'phpdoc_add_missing_param_annotation' => true,
            'elseif' => true,
            'no_empty_statement' => true,
            'no_empty_comment' => true,
            'no_empty_phpdoc' => true,
            'no_null_property_initialization' => true,
            'normalize_index_brace' => true,
            'heredoc_to_nowdoc' => true,
            'no_useless_else' => true,
            'no_useless_return' => true,
            'no_superfluous_phpdoc_tags' => true,
            'blank_line_after_namespace' => true,
            'combine_consecutive_issets' => true,
            'new_with_braces' => true,
            'no_alias_functions' => true,
            'no_blank_lines_after_class_opening' => true,
            'nullable_type_declaration_for_default_null_value' => true,
            'return_assignment' => true,
            'simplified_null_return' => true,
            'void_return' => true,
            'phpdoc_trim_consecutive_blank_line_separation' => false
        ])
    ->setFinder($finder)
;
