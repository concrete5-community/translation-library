<?php

namespace C5TL\Test;

use C5TL\ParserFactory;

class DynamicParserTest extends TestCase
{
    public function testDynamicParser()
    {
        $factory = new ParserFactory();
        $parser = $factory->getParserByHandle('dynamic');
        $this->assertNotNull($parser);
        $this->assertInstanceOf('C5TL\Parser\Dynamic', $parser);
        $subParsers = $parser->getSubParsers();
        $this->assertSame(array_values($subParsers), $subParsers);
        $this->assertNull($parser->getSubParserByHandle('this does not exist'));
    }

    public static function provideRequiredParserHandles()
    {
        return [
            ['area'],
            ['attribute_key'],
            ['attribute_key_category'],
            ['attribute_set'],
            ['attribute_type'],
            ['authentication_type'],
            ['express_form_field_set'],
            ['group'],
            ['group_set'],
            ['job_set'],
            ['permission_access_entity_type'],
            ['permission_key'],
            ['permission_key_category'],
            ['select_attribute_value'],
            ['tree'],
        ];
    }

    /**
     * @dataProvider provideRequiredParserHandles
     */
    public function testRequiredParsers($handle)
    {
        $factory = new ParserFactory();
        $parser = $factory->getParserByHandle('dynamic');
        $subParser = $parser->getSubParserByHandle($handle);
        $this->assertNotNull($subParser);
        $this->assertInstanceOf('C5TL\Parser\DynamicItem\DynamicItem', $subParser);
        $this->assertSame($handle, $subParser->getDynamicItemsParserHandler());
    }
}
