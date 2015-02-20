<?php
namespace C5TL\Parser\DynamicItem;

/**
 * Extract translatable data from JobSets
 */
class JobSet extends DynamicItem
{
    /**
     * @see \C5TL\Parser\DynamicItem::getParsedItemNames()
     */
    public function getParsedItemNames()
    {
        return function_exists('t') ? t('Job set names') : 'Job set names';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::getClassNameForExtractor()
     */
    protected function getClassNameForExtractor()
    {
        return '\Concrete\Core\Job\Set';
    }

    /**
     * @see \C5TL\Parser\DynamicItem::parseManual()
     */
    public function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
        if (class_exists('\JobSet', true)) {
            foreach (\JobSet::getList() as $js) {
                $this->addTranslation($translations, $js->getJobSetName(), 'JobSetName');
            }
        }
    }
}
