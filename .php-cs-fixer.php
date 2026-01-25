<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.92.5|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@PER-CS:risky' => true,
        // PHP arrays should be declared using the configured syntax.
        'array_syntax' => ['syntax' => 'long'],
        // Classes, constants, properties, and methods MUST have visibility declared, and keyword modifiers MUST be in the following order: inheritance modifier (`abstract` or `final`), visibility modifier (`public`, `protected`, or `private`), set-visibility modifier (`public(set)`, `protected(set)`, or `private(set)`), scope modifier (`static`), mutation modifier (`readonly`), type declaration, name.
        'modifier_keywords' => ['elements' => ['method', 'property']],
        // Empty body of class, interface, trait, enum or function must be abbreviated as `{}` and placed on the same line as the previous symbol, separated by a single space.
        'single_line_empty_body' => false,
        // Arguments lists, array destructuring lists, arrays that are multi-line, `match`-lines and parameters lists must have a trailing comma.
        'trailing_comma_in_multiline' => ['after_heredoc' => false, 'elements' => ['arrays']],
        // Classes, constants, properties, and methods MUST have visibility declared, and keyword modifiers MUST be in the following order: inheritance modifier (`abstract` or `final`), visibility modifier (`public`, `protected`, or `private`), set-visibility modifier (`public(set)`, `protected(set)`, or `private(set)`), scope modifier (`static`), mutation modifier (`readonly`), type declaration, name.
        'visibility_required' => ['elements' => ['method', 'property']],
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->in(__DIR__)
        // ->exclude([
        //     'folder-to-exclude',
        // ])
        // ->append([
        //     'file-to-include',
        // ])
    )
;
