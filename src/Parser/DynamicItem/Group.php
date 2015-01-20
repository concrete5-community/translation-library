<?php
namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from Groups
 */
class Group extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected static function getClassNameForExtractor()
    {
        return '\Concrete\Core\User\Group\Group';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public static function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        if (class_exists('\GroupList', true)) {
            $gl = new \GroupList(null, false, true);
            foreach ($gl->getGroupList() as $g) {
                self::addTranslation($translations, $g->getGroupName(), 'GroupName');
                self::addTranslation($translations, $g->getGroupDescription(), 'GroupDescription');
            }
        }
    }
}
