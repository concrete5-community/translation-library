<?php

namespace C5TL\Test;

abstract class TestCase4 extends TestCaseBase
{
    final public static function setupBeforeClass()
    {
        static::doSetUpBeforeClass();
    }

    final public function setUp()
    {
        static::doSetUp();
    }
}
