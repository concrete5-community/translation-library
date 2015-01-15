<?php
namespace C5TL;

/**
 * Variuos gettext-related helper functions
 */
class Gettext
{
    /**
     * Returns the plural rule for a specific locale
     * @param string $locale The locale identifier
     * @return string
     * @throws \Exception Throws an \Exception if $locale is not a valid locale identifier of if something else goes wrong
     * @link http://git.savannah.gnu.org/cgit/gettext.git/tree/gettext-tools/src/plural-table.c
     */
    public static function getPluralRule($locale)
    {
        static $cache;
        if (!is_array($cache)) {
            $cache = array();
        }
        if (!array_key_exists($locale, $cache)) {
            try {
                $tempDirectory = \C5TL\Options::getTemporaryDirectory();
                $tempFilePOT = @tempnam($tempDirectory, 'cil');
                if ($tempFilePOT === false) {
                    throw new \Exception(t('Unable to create a temporary file'));
                }
                if (@file_put_contents($tempFilePOT, <<<EOT
msgid ""
msgstr ""
"Project-Id-Version: \\n"
"Report-Msgid-Bugs-To: \\n"
"POT-Creation-Date: 2010-01-01 00:00+0000\\n"
"PO-Revision-Date: 2010-01-01 00:00+0000\\n"
"Last-Translator: \\n"
"Language-Team: \\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
EOT
                ) === false) {
                    throw new \Exception(t('Unable to write to a temporary file'));
                }
                $tempFilePO = @tempnam($tempDirectory, 'cil');
                if ($tempFilePO === false) {
                    throw new \Exception(t('Unable to create a temporary file'));
                }
                @exec('msginit --input=' . escapeshellarg($tempFilePOT) . ' --output-file=' . escapeshellarg($tempFilePO) . ' --locale=' . escapeshellarg($locale) . ' --no-translator --no-wrap 2>&1', $output, $rc);
                @unlink($tempFilePOT);
                if ($rc !== 0) {
                    throw new \Exception('msginit failed: ' . implode(PHP_EOL, $output));
                }
                $translations = \Gettext\Extractors\Po::fromFile($tempFilePO);
                @unlink($tempFilePO);
                $pluralRule = $translations->getHeader('Plural-Forms');
                if (!(is_string($pluralRule) && strlen($pluralRule))) {
                    throw new \Exception(t('Unrecognized locale: %s', $locale));
                }
                $cache[$locale] = $pluralRule;
            } catch (\Exception $x) {
                if (isset($tempFilePO) && is_file($tempFilePO)) {
                    @unlink($tempFilePO);
                }
                if (isset($tempFilePOT) && is_file($tempFilePOT)) {
                    @unlink($tempFilePOT);
                }
                throw $x;
            }
        }

        return $cache[$locale];
    }
}
