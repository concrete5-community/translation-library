<?php

namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from GroupSets.
 */
class GroupSet extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getParsedItemNames()
     */
    public function getParsedItemNames()
    {
        return function_exists('t') ? t('User group set names') : 'User group set names';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected function getClassNameForExtractor()
    {
        return '\Concrete\Core\User\Group\GroupSet';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        if (class_exists('\GroupSet', true)) {
            foreach (\GroupSet::getList() as $gs) {
                $this->addTranslation($translations, $gs->getGroupSetName(), 'GroupSetName');
            }
        }
    }
}
