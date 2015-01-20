<?php
namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from PermissionKeys
 */
class PermissionKey extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected static function getClassNameForExtractor()
    {
        return '\Concrete\Core\Permission\Key\Key';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public static function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        if (class_exists('\PermissionKeyCategory', true) && method_exists('\PermissionKeyCategory', 'getList')) {
            foreach (\PermissionKeyCategory::getList() as $pkc) {
                $pkcHandle = $pkc->getPermissionKeyCategoryHandle();
                foreach (\PermissionKey::getList($pkcHandle) as $pk) {
                    self::addTranslation($translations, $pk->getPermissionKeyName(), 'PermissionKeyName');
                    self::addTranslation($translations, $pk->getPermissionKeyDescription(), 'PermissionKeyDescription');
                }
            }
        }
    }
}
