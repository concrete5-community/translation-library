<?php

namespace C5TL\Test;

abstract class TestCase8 extends TestCaseBase
{
    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestCase::setupBeforeClass()
     */
    final public static function setupBeforeClass(): void
    {
        static::doSetUpBeforeClass();
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    final public function setUp(): void
    {
        static::doSetUp();
    }
}
