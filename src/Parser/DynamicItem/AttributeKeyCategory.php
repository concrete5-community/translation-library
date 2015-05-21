<?php

namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from AttributeKeyCategories.
 */
class AttributeKeyCategory extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getParsedItemNames()
     */
    public function getParsedItemNames()
    {
        return function_exists('t') ? t('Attribute categories') : 'Attribute categories';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected function getClassNameForExtractor()
    {
        return '\Concrete\Core\Attribute\Key\Category';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        $akcNameMap = array(
            'collection' => 'Page attributes',
            'user' => 'User attributes',
            'file' => 'File attributes',
        );
        if (version_compare($concrete5version, '5.7') < 0) {
            $akcClass = '\AttributeKeyCategory';
        } else {
            $akcClass = '\Concrete\Core\Attribute\Key\Category';
        }
        if (class_exists($akcClass, true) && method_exists($akcClass, 'getList')) {
            foreach (call_user_func($akcClass.'::getList') as $akc) {
                $akcHandle = $akc->getAttributeKeyCategoryHandle();
                $this->addTranslation($translations, isset($akcNameMap[$akcHandle]) ? $akcNameMap[$akcHandle] : ucwords(str_replace(array('_', '-', '/'), ' ', $akcHandle)));
            }
        }
    }
}
