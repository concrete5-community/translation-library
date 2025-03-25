<?php

namespace C5TL;

/**
 * A class that provides parsers.
 */
class ParserFactory
{
    /**
     * @var \C5TL\Parser[]
     */
    private $parsers = array();

    public function __construct()
    {
        foreach ($this->getDefaultParsers() as $parser) {
            $this->registerParser($parser);
        }
    }
    /**
     * * @return \C5TL\Parser[]
     */
    public function getParsers()
    {
        return array_values($this->parsers);
    }

    /**
     * @param string|mixed $handle
     *
     * @return \C5TL\Parser|null
     */
    public function getParserByHandle($handle)
    {
        return is_string($handle) && isset($this->parsers[$handle]) ? $this->parsers[$handle] : null;
    }

    /**
     * @return $this
     */
    public function registerParser(Parser $parser)
    {
        $this->parsers[$parser->getParserHandle()] = $parser;

        return $this;
    }

    /**
     * @return \C5TL\Parser[]
     */
    private function getDefaultParsers()
    {
        $result = array();
        $dir = __DIR__ . '/Parser';
        if (is_dir($dir) && is_readable($dir)) {
            $matches = null;
            foreach (scandir($dir) as $item) {
                if (($item[0] !== '.') && preg_match('/^(.+)\.php$/i', $item, $matches)) {
                    $fqClassName = '\\' . __NAMESPACE__ . '\\Parser\\' . $matches[1];

                    if (method_exists($fqClassName, 'isSupported')) {
                        if (!$fqClassName::isSupported()) {
                            continue;
                        }
                    }

                    $result[] = new $fqClassName();
                }
            }
        }

        return $result;
    }
}
