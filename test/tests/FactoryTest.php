<?php

namespace C5TL\Test;

use C5TL\ParserFactory;

class FactoryTest extends TestCase
{
    public function testFactory()
    {
        $factory = new ParserFactory();
        $parsers = $factory->getParsers();
        $this->assertSame('array', gettype($parsers));
        $this->assertNotSame([], $parsers);
        $this->assertSame(array_values($parsers), $parsers);
        $this->assertNull($factory->getParserByHandle('this does not exist'));
    }

    public static function provideRequiredParserHandles()
    {
        return [
            ['block_templates'],
            ['cif'],
            ['config_files'],
            ['dynamic'],
            ['php'],
            ['theme_presets'],
            ['twig'],
        ];
    }

    /**
     * @dataProvider provideRequiredParserHandles
     */
    public function testRequiredParsers($handle)
    {
        $factory = new ParserFactory();
        $parser = $factory->getParserByHandle($handle);
        $this->assertNotNull($parser);
        $this->assertSame($handle, $parser->getParserHandle());
    }
}
