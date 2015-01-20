<?php
namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from GroupSets
 */
class GroupSet extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected static function getClassNameForExtractor()
    {
        return '\Concrete\Core\User\Group\GroupSet';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public static function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        if (class_exists('\GroupSet', true)) {
            foreach (\GroupSet::getList() as $gs) {
                self::addTranslation($translations, $as->getGroupSetName(), 'GroupSetName');
            }
        }
    }
}
