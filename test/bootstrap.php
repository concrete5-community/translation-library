<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (class_exists('PHPUnit\\Runner\\Version') && version_compare(PHPUnit\Runner\Version::id(), '8') >= 0) {
    class_alias('C5TL\\Test\\TestCase8', 'C5TL\\Test\\TestCase');
} elseif (class_exists('PHPUnit\\Runner\\Version') && version_compare(PHPUnit\Runner\Version::id(), '6') >= 0) {
    class_alias('C5TL\\Test\\TestCase6', 'C5TL\\Test\\TestCase');
} else {
    class_alias('C5TL\\Test\\TestCase4', 'C5TL\\Test\\TestCase');
}
