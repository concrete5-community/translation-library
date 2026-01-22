<?php

namespace C5TL\Parser;

use Concrete\Core\Support\Facade\Application;

/**
 * Extract translatable strings from the getTranslatableStrings() method of installed packages.
 */
class InstalledPackage extends \C5TL\Parser
{
    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser::getParserName()
     */
    public function getParserName()
    {
        return function_exists('t') ? t('Installed Packages Parser') : 'Installed Packages Parser';
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
        return version_compare($version, '8.1') >= 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser::parseDirectoryDo()
     */
    protected function parseDirectoryDo(\Gettext\Translations $translations, $rootDirectory, $relativePath, $subParsersFilter, $exclude3rdParty)
    {
        if (!defined('DIR_BASE') || strpos($rootDirectory, DIR_BASE . '/') !== 0) {
            return;
        }
        $packageHandle = $this->extractPackageHandle($relativePath);
        if ($packageHandle === '') {
            return;
        }
        $packageController = $this->getInstalledPackageController($packageHandle);
        if ($packageController === null) {
            return;
        }
        if (!method_exists($packageController, 'getTranslatableStrings')) {
            return;
        }
        $packageController->getTranslatableStrings($translations);
    }

    /**
     * @param non-empty-string|mixed $relativePath
     *
     * @return non-empty-string|string
     */
    private function extractPackageHandle($relativePath)
    {
        $m = null;

        return is_string($relativePath) && preg_match('#(^|/)packages/(?<handle>[a-z]([a-z0-9_]*[a-z0-9])?)$#', $relativePath, $m) ? $m['handle'] : '';
    }

    /**
     * @param non-empty-string $packageHandle
     *
     * @return \Concrete\Core\Package\Package|null
     */
    private function getInstalledPackageController($packageHandle)
    {
        if (!defined('C5_EXECUTE')
            || !class_exists('Concrete\Core\Support\Facade\Application')
            || !class_exists('Concrete\Core\Package\PackageService')
        ) {
            return null;
        }
        $app = Application::getFacadeApplication();
        if (!$app->isInstalled()) {
            return null;
        }
        $packageService = $app->make('Concrete\Core\Package\PackageService');
        $packageEntity = $packageService->getByHandle($packageHandle);
        if ($packageEntity === null) {
            return null;
        }

        return $packageEntity->getController();
    }
}
