<?php
namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from AttributeTypes
 */
class AttributeType extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected static function getClassNameForExtractor()
    {
        return '\Concrete\Core\Attribute\Type';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public static function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        if (class_exists('\AttributeType', true)) {
            foreach (\AttributeType::getList() as $at) {
                self::addTranslation($translations, $at->getAttributeTypeName(), 'AttributeTypeName');
            }
        }
    }
}
