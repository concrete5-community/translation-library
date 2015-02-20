<?php
namespace C5TL\Parser;

/**
 * Extract translatable strings from block type templates
 */
class Dynamic extends \C5TL\Parser
{
    /**
     * @see \C5TL\Parser::getParserName()
     */
    public function getParserName()
    {
        return function_exists('t') ? t('Block templates') : 'Block templates';
    }

    /**
     * @see \C5TL\Parser::canParseRunningConcrete5()
     */
    public function canParseRunningConcrete5()
    {
        return true;
    }

    /**
     * @see \C5TL\Parser::parseRunningConcrete5Do()
     */
    protected function parseRunningConcrete5Do(\Gettext\Translations $translations, $concrete5version, $subParsersFilter)
    {
        foreach ($this->getSubParsers() as $dynamicItemParser) {
            if ((!is_array($subParsersFilter)) || in_array($dynamicItemParser->getDynamicItemsParserHandler(), $subParsersFilter)) {
                $dynamicItemParser->parse($translations, $concrete5version);
            }
        }
    }

    /**
     * @see \C5TL\Parser::getSubParserHandles()
     */
    public function getSubParserHandles()
    {
        $result = array();
        foreach ($this->getSubParsers() as $dynamicItemParser) {
            $result[] = $dynamicItemParser->getDynamicItemsParserHandler();
        }

        return $result;
    }

    /**
     * Returns the fully-qualified class names of all the sub-parsers
     * @return array[\C5TL\Parser\DynamicItem\DynamicItem]
     */
    private function getSubParsers()
    {
        $result = array();
        $dir = __DIR__.'/DynamicItem';
        if (is_dir($dir) && is_readable($dir)) {
            $matches = null;
            foreach (scandir($dir) as $item) {
                if (($item[0] !== '.') && preg_match('/^(.+)\.php$/i', $item, $matches) && ($matches[1] !== 'DynamicItem')) {
                    $fqClassName = '\\'.__NAMESPACE__.'\\DynamicItem\\'.$matches[1];
                    $result[] = new $fqClassName();
                }
            }
        }

        return $result;
    }
}
