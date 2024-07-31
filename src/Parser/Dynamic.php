<?php

namespace C5TL\Parser;

use C5TL\Parser\DynamicItem\DynamicItem;

/**
 * Extract translatable strings from block type templates.
 */
class Dynamic extends \C5TL\Parser
{
    private $subParsers = array();

    public function __construct()
    {
        foreach ($this->getDefaultSubParsers() as $subParser) {
            $this->registerSubParser($subParser);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser::getParserName()
     */
    public function getParserName()
    {
        return function_exists('t') ? t('Block templates') : 'Block templates';
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser::canParseRunningConcrete5()
     */
    public function canParseRunningConcrete5()
    {
        return true;
    }

    /**
     * @return $this
     */
    public function registerSubParser(DynamicItem $subParser)
    {
        $this->subParsers[$subParser->getDynamicItemsParserHandler()] = $subParser;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser::parseRunningConcrete5Do()
     */
    protected function parseRunningConcrete5Do(\Gettext\Translations $translations, $concrete5version, $subParsersFilter)
    {
        foreach ($this->getSubParsers() as $dynamicItemParser) {
            if ((!is_array($subParsersFilter)) || in_array($dynamicItemParser->getDynamicItemsParserHandler(), $subParsersFilter, true)) {
                $dynamicItemParser->parse($translations, $concrete5version);
            }
        }
    }

    /**
     * Returns the fully-qualified class names of all the sub-parsers.
     *
     * @return \C5TL\Parser\DynamicItem\DynamicItem[]
     */
    public function getSubParsers()
    {
        return array_values($this->subParsers);
    }

    /**
     * @param string|mixed $handle
     *
     * @return \C5TL\Parser\DynamicItem\DynamicItem|null
     */
    public function getSubParserByHandle($handle)
    {
        return is_string($handle) && isset($this->subParsers[$handle]) ? $this->subParsers[$handle] : null;
    }

    /**
     * @return \C5TL\Parser\DynamicItem\DynamicItem[]
     */
    private function getDefaultSubParsers()
    {
        $result = array();
        $dir = __DIR__ . '/DynamicItem';
        if (is_dir($dir) && is_readable($dir)) {
            $matches = null;
            foreach (scandir($dir) as $item) {
                if (($item[0] !== '.') && preg_match('/^(.+)\.php$/i', $item, $matches) && ($matches[1] !== 'DynamicItem')) {
                    $fqClassName = '\\' . __NAMESPACE__ . '\\DynamicItem\\' . $matches[1];
                    $result[] = new $fqClassName();
                }
            }
        }

        return $result;
    }
}
