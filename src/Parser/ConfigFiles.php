<?php

namespace C5TL\Parser;

/**
 * Extract translatable strings from core configuration PHP files.
 */
class ConfigFiles extends \C5TL\Parser
{
    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser::getParserName()
     */
    public function getParserName()
    {
        return function_exists('t') ? t('Core PHP Configurations Parser') : 'Core PHP Configurations Parser';
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser::canParseDirectory()
     */
    public function canParseDirectory()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser::canParseConcreteVersion()
     */
    public function canParseConcreteVersion($version)
    {
        return version_compare($version, '5.7') >= 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser::parseDirectoryDo()
     */
    protected function parseDirectoryDo(\Gettext\Translations $translations, $rootDirectory, $relativePath, $subParsersFilter, $exclude3rdParty)
    {
        $directoryAlternatives = array(
            array('', 'application', 'concrete'),
            array('config/generated_overrides', 'config'),
        );
        switch ($relativePath) {
            case '':
                $directoryAlternatives = array('application/config/generated_overrides', 'application/config', 'concrete/config');
                break;
            case 'application':
                $directoryAlternatives = array('config/generated_overrides', 'config');
                break;
            case 'concrete':
                $directoryAlternatives = array('config');
                break;
            default:
                return;
        }
        $prefix = ($relativePath === '') ? '' : "$relativePath/";
        $this->parseFileTypes($translations, $rootDirectory, $prefix, $directoryAlternatives);
    }

    /**
     * Parse the file type names.
     *
     * @param \Gettext\Translations $translations
     * @param string                $rootDirectory
     * @param string                $prefix
     * @param string[]              $directoryAlternatives
     */
    private function parseFileTypes(\Gettext\Translations $translations, $rootDirectory, $prefix, $directoryAlternatives)
    {
        foreach ($directoryAlternatives as $subDir) {
            $rel = ($subDir === '') ? 'app.php' : "$subDir/app.php";
            $fileAbs = $rootDirectory.'/'.$rel;
            if (!is_file($fileAbs)) {
                continue;
            }
            $fileRel = $prefix.$rel;
            $configFile = new \C5TL\Util\ConfigFile($fileAbs);
            $config = $configFile->getArray();
            if (isset($config['file_types']) && is_array($config['file_types'])) {
                $fileTypes = $config['file_types'];
                foreach (array_keys($fileTypes) as $fileType) {
                    $translation = $translations->insert('', $fileType);
                    $translation->addReference($fileRel);
                }
            }
        }
    }
}
