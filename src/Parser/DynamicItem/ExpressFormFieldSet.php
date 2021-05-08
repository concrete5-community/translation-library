<?php

namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from ExpressFormFieldSets.
 */
class ExpressFormFieldSet extends DynamicItem
{
    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser\DynamicItem\DynamicItem::getParsedItemNames()
     */
    public function getParsedItemNames()
    {
        return function_exists('t') ? t('Express Form Field Sets') : 'Express Form Field Sets';
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser\DynamicItem\DynamicItem::getClassNameForExtractor()
     */
    protected function getClassNameForExtractor()
    {
        return '\Concrete\Core\Entity\Express\FieldSet';
    }
}
