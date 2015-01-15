<?php
namespace C5TL\Parser;

/**
 * Extract translatable strings from PHP files (functions t(), tc() and t2())
 */
class Php extends \C5TL\Parser
{
    /**
     * @see \C5TL\Parser::getParserName()
     */
    public function getParserName()
    {
        return 'PHP Parser';
    }

    /**
     * @see \C5TL\Parser::parseDo()
     */
    protected function parseDo(\Gettext\Translations $translations, $rootDirectory, $relativePath)
    {
        $phpFiles = array();
        foreach ($this->getDirectoryStructure($rootDirectory) as $child) {
            $fullDirectoryPath = "$rootDirectory/$child";
            $contents = @scandir($fullDirectoryPath);
            if ($contents === false) {
                throw new \Exception("Unable to parse directory $fullDirectoryPath");
            }
            foreach ($contents as $file) {
                if (strpos($file, '.') !== 0) {
                    $fullFilePath = "$fullDirectoryPath/$file";
                    if (preg_match('/^(.*)\.php$/', $file) && is_file($fullFilePath)) {
                        $phpFiles[] = "$child/$file";
                    }
                }
            }
        }
        if (count($phpFiles) > 0) {
            $initialDirectory = @getcwd();
            if ($initialDirectory === false) {
                throw new \Exception('Unable to determine the current working directory');
            }
            if (@chdir($rootDirectory) === false) {
                throw new \Exception('Unable to switch to directory ' . $rootDirectory);
            }
            try {
                $tempDirectory = \C5TL\Options::getTemporaryDirectory();
                $tempFileList = @tempnam($tempDirectory, 'cil');
                if ($tempFileList === false) {
                    throw new \Exception(t('Unable to create a temporary file'));
                }
                if (@file_put_contents($tempFileList, implode("\n", $phpFiles)) === false) {
                    global $php_errormsg;
                    if (isset($php_errormsg) && strlen($php_errormsg)) {
                        throw new \Exception("Error writing a temporary file: $php_errormsg");
                    } else {
                        throw new \Exception("Error writing a temporary file");
                    }
                }
                $tempFilePot = @tempnam($tempDirectory, 'cil');
                if ($tempFilePot === false) {
                    throw new \Exception(t('Unable to create a temporary file'));
                }
                $line = 'xgettext';
                $line .= ' --default-domain=messages'; // Domain
                $line .= ' --output=' . escapeshellarg(basename($tempFilePot)); // Output .pot file name
                $line .= ' --output-dir=' . escapeshellarg(dirname($tempFilePot)); // Output .pot folder name
                $line .= ' --language=PHP'; // Source files are in php
                $line .= ' --from-code=UTF-8'; // Source files are in utf-8
                $line .= ' --add-comments=i18n'; // Place comment blocks preceding keyword lines in output file if they start with '// i18n: '
                $line .= ' --keyword'; // Don't use default keywords
                $line .= ' --keyword=t:1'; // Look for the first argument of the "t" function for extracting translatable text in singular form
                $line .= ' --keyword=t2:1,2'; // Look for the first and second arguments of the "t2" function for extracting both the singular and plural forms
                $line .= ' --keyword=tc:1c,2'; // Look for the first argument of the "tc" function for extracting translation context, and the second argument is the translatable text in singular form.
                $line .= ' --no-escape'; // Do not use C escapes in output
                $line .= ' --indent'; // Write using indented style
                $line .= ' --add-location'; // Generate '#: filename:line' lines
                $line .= ' --files-from=' . escapeshellarg($tempFileList); // Get list of input files from file
                $line .= ' 2>&1';
                $output = array();
                $rc = null;
                @exec($line, $output, $rc);
                @unlink($tempFileList);
                unset($tempFileList);
                if (!is_int($rc)) {
                    $rc = -1;
                }
                if (!is_array($output)) {
                    $output = array();
                }
                if ($rc !== 0) {
                    throw new \Exception("xgettext failed: " . implode("\n", $output));
                }
                $newTranslations = \Gettext\Translations::fromPoFile($tempFilePot);
                @unlink($tempFilePot);
                unset($tempFilePot);
            } catch (\Exception $x) {
                @chdir($initialDirectory);
                if (isset($tempFilePot) && @is_file($tempFilePot)) {
                    @unlink($tempFilePot);
                }
                if (isset($tempFileList) && @is_file($tempFileList)) {
                    @unlink($tempFileList);
                }
                throw $x;
            }
            if ($newTranslations->count() > 0) {
                if (strlen($relativePath) > 0) {
                    foreach ($newTranslations as $newTranslation) {
                        $references = $newTranslation->getReferences();
                        $newTranslation->wipeReferences();
                        foreach ($references as $reference) {
                            $newTranslation->addReference($relativePath.'/'.$reference[0], $reference[1]);
                        }
                    }
                }
                if ($translations->count() > 0) {
                    foreach ($newTranslations as $newTranslation) {
                        $oldTranslation = $translations->find($newTranslation);
                        if ($oldTranslation) {
                            $oldTranslation->mergeWith($newTranslation);
                        } else {
                            $translations[] = $newTranslation;
                        }
                    }
                } else {
                    foreach ($newTranslations as $newTranslation) {
                        $translations[] = $newTranslation;
                    }
                }
            }
        }
    }
}
