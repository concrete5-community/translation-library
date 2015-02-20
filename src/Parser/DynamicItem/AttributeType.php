<?php
namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from AttributeTypes
 */
class AttributeType extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getParsedItemNames()
     */
    public function getParsedItemNames()
    {
        return function_exists('t') ? t('Attribute type names') : 'Attribute type names';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected function getClassNameForExtractor()
    {
        return '\Concrete\Core\Attribute\Type';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        if (class_exists('\AttributeType', true)) {
            foreach (\AttributeType::getList() as $at) {
                $this->addTranslation($translations, $at->getAttributeTypeName(), 'AttributeTypeName');
            }
        }
    }
}
