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
        $this->assertNotSame(array(), $parsers);
        $this->assertSame(array_values($parsers), $parsers);
        $this->assertNull($factory->getParserByHandle('this does not exist'));
    }

    public static function provideRequiredParserHandles()
    {
        return array(
            array('block_templates'),
            array('cif'),
            array('config_files'),
            array('dynamic'),
            array('php'),
            array('theme_presets'),
        );
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
