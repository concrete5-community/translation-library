<?php
namespace C5TL\Parser;

/**
 * Extract translatable strings from themes presets
 */
class ThemePresets extends \C5TL\Parser
{
    /**
     * @see \C5TL\Parser::getParserName()
     */
    public function getParserName()
    {
        return 'Themes presets';
    }

    /**
     * @see \C5TL\Parser::canParseDirectory()
     */
    public function canParseDirectory()
    {
        return true;
    }

    /**
     * @see \C5TL\Parser::parseDirectoryDo()
     */
    protected function parseDirectoryDo(\Gettext\Translations $translations, $rootDirectory, $relativePath)
    {
        $themesPresets = array();
        $prefix = ($relativePath === '') ? '' : "$relativePath/";
        foreach ($this->getDirectoryStructure($rootDirectory) as $child) {
            $shownChild = $prefix . $child;
            if (preg_match('%(?:^|/)themes/\w+/css/presets$%', $shownChild, $matches)) {
                $presetsAbsDirectory = "$rootDirectory/$child";
                $hDir = @opendir($presetsAbsDirectory);
                if ($hDir === false) {
                    throw new \Exception("Unable to open directory $presetsAbsDirectory");
                }
                try {
                    while (($file = @readdir($hDir)) !== false) {
                        if (($file[0] !== '.') && preg_match('/[^.].*\.less$/i', $file)) {
                            $fileAbs = "$presetsAbsDirectory/$file";
                            if (is_file($fileAbs)) {
                                $content = @file_get_contents($fileAbs);
                                if ($content === false) {
                                    throw new \Exception("Error reading file '$fileAbs'");
                                }
                                $content = str_replace("\r", "\n", str_replace("\r\n", "\n", $content));
                                // Strip multiline comments
                                $content = preg_replace_callback(
                                    '|/\*.*?\*/|s',
                                    function ($matches) {
                                        return str_repeat("\n", substr_count($matches[0], "\n"));
                                    },
                                    $content
                                );
                                foreach (array("'", '"') as $quote) {
                                    if (preg_match('%(?:^|\\n|;)[ \\t]*@preset-name:\\s*' . $quote . '([^' . $quote . ']*)' . $quote . '\\s*(?:;|$)%s', $content, $matches)) {
                                        $presetName = $matches[1];
                                        $presetLine = null;
                                        $p = strpos($content, $matches[0]);
                                        if ($p !== false) {
                                            $presetLine = substr_count(substr($content, 0, $p), "\n") + 1;
                                        }
                                        if (!isset($themesPresets[$presetName])) {
                                            $themesPresets[$presetName] = array();
                                        }
                                        $themesPresets[$presetName][] = array($shownChild . "/$file", $presetLine);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $x) {
                    @closedir($hDir);
                    throw $x;
                }
                @closedir($hDir);
            }
        }
        foreach ($themesPresets as $themesPreset => $references) {
            $translation = $translations->insert('PresetName', ucwords(str_replace(array('_', '-', '/'), ' ', $themesPreset)));
            foreach ($references as $reference) {
                $translation->addReference($reference);
            }
        }
    }
}
