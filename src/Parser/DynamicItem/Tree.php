<?php

namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from AttributeSets.
 */
class Tree extends DynamicItem
{
    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser\DynamicItem::getParsedItemNames()
     */
    public function getParsedItemNames()
    {
        return function_exists('t') ? t('Trees and Topics') : 'Trees and Topics';
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected function getClassNameForExtractor()
    {
        return '\Concrete\Core\Tree\Tree';
    }
}
