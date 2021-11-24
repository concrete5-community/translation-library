<?php

namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from Areas.
 */
class Area extends DynamicItem
{
    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser\DynamicItem\DynamicItem::getParsedItemNames()
     */
    public function getParsedItemNames()
    {
        return function_exists('t') ? t('Area names') : 'Area names';
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser\DynamicItem\DynamicItem::getClassNameForExtractor()
     */
    protected function getClassNameForExtractor()
    {
        return '\Concrete\Core\Area\Area';
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser\DynamicItem\DynamicItem::parseManual()
     */
    public function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        $db = \Loader::db();
        $sql = 'select distinct (binary arHandle) as AreaName from Areas order by binary arHandle';
        if (method_exists($db, 'executeQuery')) {
            $rs = $db->executeQuery($sql);
        } else {
            $rs = $db->Execute($sql);
        }
        if (method_exists($rs, 'fetchAssociative')) {
            $fetcher = 'fetchAssociative';
        } else {
            $fetcher = 'FetchRow';
        }
        while ($row = $rs->$fetcher()) {
            $this->addTranslation($translations, $row['AreaName'], 'AreaName');
        }
        if (method_exists($rs, 'Close')) {
            $rs->Close();
        }
    }
}
