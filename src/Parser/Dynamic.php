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
        return 'Block templates';
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
    protected function parseRunningConcrete5Do(\Gettext\Translations $translations, $concrete5version)
    {
        foreach (self::getAllDynamicItemClasses() as $fq) {
            call_user_func($fq . '::parse', $translations, $concrete5version);
        }
    }

    /**
     * Returns the fully-qualified class names of all the DynamicItems
     * @return array[string]
     */
    private static function getAllDynamicItemClasses()
    {
        $result = array();
        $dir = __DIR__ . '/DynamicItem';
        if (is_dir($dir) && is_readable($dir)) {
            foreach (scandir($dir) as $item) {
                if (preg_match('/^(.+)\.php$/i', $item, $matches) && ($matches[1] !== 'DynamicItem')) {
                    $result[] = '\\' . __NAMESPACE__ . '\\DynamicItem\\' . $matches[1];
                }
            }
        }

        return $result;
    }
}
