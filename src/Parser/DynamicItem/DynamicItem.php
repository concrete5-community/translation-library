<?php
namespace C5TL\Parser\DynamicItem;

/**
 * Base class for all DynamicItem parsers
 */
abstract class DynamicItem
{
    /**
     * Extract specific items from the running concrete5.
     * @param \Gettext\Translations $translations Found translations will be appended here.
     * @param string $concrete5version The version of the running concrete5 instance.
     */
    final public static function parse(\Gettext\Translations $translations, $concrete5version)
    {
        $fqClassName = static::getClassNameForExtractor();
        if (is_string($fqClassName) && ($fqClassName !== '') && class_exists($fqClassName, true) && method_exists($fqClassName, 'exportTranslations')) {
            $translations->mergeWith(call_user_func($fqClassName .'::exportTranslations'));
        } else {
            static::parseManual($translations, $concrete5version);
        }
    }

    /**
     * Returns the fully qualified class name that extracts automatically strings.
     * @return string
     */
    protected static function getClassNameForExtractor()
    {
        return '';
    }

    /**
     * Manual parsing of items.
     * @param \Gettext\Translations $translations Found translations will be appended here.
     * @param string $concrete5version The version of the running concrete5 instance.
     */
    protected static function parseManual(\Gettext\Translations $translations, $concrete5version)
    {
    }

    /**
     * Adds a translation to the \Gettext\Translations object
     * @param \Gettext\Translations $translations
     * @param string $string
     * @param string $context
     */
    final protected static function addTranslation(\Gettext\Translations $translations, $string, $context = '')
    {
        if (is_string($string) && ($string !== '')) {
            $translations->insert($context, $string);
        }
    }
}
