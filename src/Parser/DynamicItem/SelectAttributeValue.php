<?php
namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from SelectAttributeValues
 */
class SelectAttributeValue extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected static function getClassNameForExtractor()
    {
        return '\Concrete\Attribute\Select\Option';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public static function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        if (class_exists('\AttributeKeyCategory', true) && class_exists('\AttributeKey', true) && class_exists('\AttributeType', true)) {
            foreach (\AttributeKeyCategory::getList() as $akc) {
                $akcHandle = $akc->getAttributeKeyCategoryHandle();
                foreach (\AttributeKey::getList($akcHandle) as $ak) {
                    if ($ak->getAttributeType()->getAttributeTypeHandle() == 'select') {
                        foreach ($ak->getController()->getOptions() as $option) {
                            self::addTranslation($translations, 'SelectAttributeValue', $option->getSelectAttributeOptionValue(false));
                        }
                    }
                }
            }
        }
    }
}
