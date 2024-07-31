<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.61.0|configurator
 * you can change this configuration by importing this file.
 */

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@PER-CS:risky' => true,

        // Empty body of class, interface, trait, enum or function must be abbreviated as `{}` and placed on the same line as the previous symbol, separated by a single space.
        'single_line_empty_body' => false,
        // Multi-line arrays, arguments list, parameters list and `match` expressions must have a trailing comma.
        'trailing_comma_in_multiline' => ['after_heredoc' => false, 'elements' => ['arrays']],
        // Visibility MUST be declared on all properties and methods; `abstract` and `final` MUST be declared before the visibility; `static` MUST be declared after the visibility.
        'visibility_required' => ['elements' => ['method','property']],
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
