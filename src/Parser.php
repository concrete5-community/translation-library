<?php
namespace C5TL;

/**
 * Base class for all the parsers
 */
abstract class Parser
{
    /**
     * Handles some stuff in memory.
     * @var array
     */
    private static $cache = array();

    /**
     * Returns the parser name
     */
    abstract public function getParserName();

    /**
     * Extracts translations.
     * @param \Gettext\Translations|null $translations The translations object where the translatable strings will be added (if null we'll create a new Translations instance).
     * @param string $rootDirectory The base directory where we start looking translations from.
     * @param string $relativePath='' The relative path (translations references will be prepended with this path).
     * @throws \Exception Throws an \Exception in case of errors.
     * @return \Gettext\Translations
     */
    public function parse($translations, $rootDirectory, $relativePath = '')
    {
        if (!is_object($translations)) {
            $translations = new \Gettext\Translations();
        }
        $dir = (is_string($rootDirectory) && strlen($rootDirectory)) ? @realpath($rootDirectory) : false;
        if (is_string($dir)) {
            $dir = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $dir), '/');
        }
        if (($dir === false) || (!is_dir($dir))) {
            throw new \Exception("Unable to find the directory $rootDirectory");
        }
        if (!@is_readable($dir)) {
            throw new \Exception("Directory not readable: $dir");
        }
        $dirRel = is_string($relativePath) ? trim(str_replace(DIRECTORY_SEPARATOR, '/', $relativePath), '/') : '';
        $this->parseDo($translations, $dir, $dirRel);

        return $translations;
    }

    /**
     * Final implementation of {@link \C5TL\Parser::parse()}
     * @param \Gettext\Translations $translations Found translatable strings will be appended here
     * @param string $rootDirectory The base directory where we start looking translations from.
     * @param string $relativePath The relative path (translations references will be prepended with this path).
     */
    abstract protected function parseDo(\Gettext\Translations $translations, $rootDirectory, $relativePath);

    /**
     * Clears the memory cache.
     */
    final public static function clearCache()
    {
        self::$cache = array();
    }

    /**
     * Returns the directory structure underneath a given directory.
     * @param string $rootDirectory The root directory
     * @param bool $exclude3rdParty=true Exclude concrete5 3rd party directories (namely directories called 'vendor' and '3rdparty')
     * @return array
     */
    protected static function getDirectoryStructure($rootDirectory, $exclude3rdParty = true)
    {
        $rootDirectory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $rootDirectory), '/');
        if (!array_key_exists(__FUNCTION__, self::$cache)) {
            self::$cache[__FUNCTION__] = array();
        }
        $cacheKey = $rootDirectory . '*' . ($exclude3rdParty ? '1' : '0');
        if (!array_key_exists($cacheKey, self::$cache)) {
            self::$cache[$cacheKey] = static::getDirectoryStructureDo('', $rootDirectory, $exclude3rdParty);
        }

        return self::$cache[$cacheKey];
    }

    /**
     * Helper function called by {@link \C5TL\Parser::getDirectoryStructure()}
     * @param string $relativePath
     * @param string $rootDirectory
     * @param bool $exclude3rdParty
     * @throws \Exception
     * @return array[string]
     */
    private static function getDirectoryStructureDo($relativePath, $rootDirectory, $exclude3rdParty)
    {
        $thisRoot = $rootDirectory;
        if (strlen($relativePath) > 0) {
            $thisRoot .= '/' . $relativePath;
        }
        $subDirs = array();
        $hDir = @opendir($thisRoot);
        if ($hDir === false) {
            throw new \Exception("Unable to open directory $rootDirectory");
        }
        while (($entry = @readdir($hDir)) !== false) {
            if (strpos($entry, '.') === 0) {
                continue;
            }
            $fullPath = $thisRoot . '/' . $entry;
            if (!is_dir($fullPath)) {
                continue;
            }
            if ($exclude3rdParty) {
                $skip = false;
                switch ($entry) {
                    case 'vendor':
                    case '3rdparty':
                        $skip = true;
                        break;
                }
                if ($skip) {
                    continue;
                }
            }
            $subDirs[] = $entry;
        }
        @closedir($hDir);
        $result = array();
        foreach ($subDirs as $subDir) {
            $rel = strlen($relativePath) ? "$relativePath/$subDir" : $subDir;
            $result = array_merge($result, static::getDirectoryStructureDo($rel, $rootDirectory, $exclude3rdParty));
            $result[] = $rel;
        }

        return $result;
    }

    /**
     * Retrieves all the available parsers
     * @return array[\C5TL\Parser]
     */
    final public static function getAllParsers()
    {
        $result = array();
        $dir = __DIR__ . '/Parser';
        if (is_dir($dir) && is_readable($dir)) {
            foreach (scandir($dir) as $item) {
                if (preg_match('/^(.+)\.php$/i', $item, $matches)) {
                    $fqClassName = '\\' . __NAMESPACE__ . '\\Parser\\' . $matches[1];
                    $result[] = new $fqClassName();
                }
            }
        }

        return $result;
    }
}
