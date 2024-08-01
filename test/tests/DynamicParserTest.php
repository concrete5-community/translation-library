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
        return array(
            array('area'),
            array('attribute_key'),
            array('attribute_key_category'),
            array('attribute_set'),
            array('attribute_type'),
            array('authentication_type'),
            array('express_form_field_set'),
            array('group'),
            array('group_set'),
            array('job_set'),
            array('permission_access_entity_type'),
            array('permission_key'),
            array('permission_key_category'),
            array('select_attribute_value'),
            array('tree'),
        );
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
