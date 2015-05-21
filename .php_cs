<?php

return Symfony\CS\Config\Config::create()
    // use default SYMFONY_LEVEL and...
    ->fixers(array(
        // Allow 'return null'
        '-empty_return',
        // Ordering use statements
        'ordered_use',
        // Comparison should be strict.
        'strict',
        // Functions should be used with $strict param
        'strict_param',
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->exclude(array('vendor'))
            ->in(__DIR__)
    )
;
