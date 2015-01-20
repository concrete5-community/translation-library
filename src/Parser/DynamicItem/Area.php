<?php
namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from Areas
 */
class Area extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected static function getClassNameForExtractor()
    {
        return '\Concrete\Core\Area\Area';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public static function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        $db = \Loader::db();
        $rs = $db->Execute('select distinct (binary arHandle) as AreaName from Areas order by arHandle');
        while ($row = $rs->FetchRow()) {
            static::addTranslation($translations, $row['AreaName'], 'AreaName');
        }
        $rs->Close();
    }
}
