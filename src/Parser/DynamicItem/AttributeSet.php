<?php
namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from AttributeSets
 */
class AttributeSet extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getParsedItemNames()
     */
    public function getParsedItemNames()
    {
        return function_exists('t') ? t('Attribute sets names') : 'Attribute sets names';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected function getClassNameForExtractor()
    {
        return '\Concrete\Core\Attribute\Set';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        if (class_exists('\AttributeKeyCategory', true) && class_exists('\AttributeSet', true)) {
            foreach (\AttributeKeyCategory::getList() as $akc) {
                foreach ($akc->getAttributeSets() as $as) {
                    $this->addTranslation($translations, $as->getAttributeSetName(), 'AttributeSetName');
                }
            }
        }
    }
}
