<?php
namespace C5TL;

/**
 * Variuos gettext-related helper functions
 */
class Gettext
{
    /**
     * Checks if a gettext command is available.
     * @param string $command One of the gettext commands
     * @return bool
     */
    public static function commandIsAvailable($command)
    {
        static $cache = array();
        if (!array_key_exists($command, $cache)) {
            $cache[$command] = false;
            $safeMode = @ini_get('safe_mode');
            if (empty($safeMode)) {
                if (function_exists('exec')) {
                    if (!in_array('exec', array_map('trim', explode(', ', strtolower(@ini_get('disable_functions')))))) {
                        $rc = 1;
                        $output = array();
                        @exec($command . ' --version 2>&1', $output, $rc);
                        if ($rc === 0) {
                            $cache[$command] = true;
                        }
                    }
                }
            }
        }

        return $cache[$command];
    }

    /**
     * Returns the plural rule for a specific locale
     * @param string $locale The locale identifier
     * @return string
     * @throws \Exception Throws an \Exception if $locale is not a valid locale identifier of if something else goes wrong
     */
    public static function getPluralRule($locale)
    {
        static $cache = array();
        if (!array_key_exists($locale, $cache)) {
            $pluralRule = '';
            if (is_string($locale) && strlen($locale)) {
                // Unicode Language Identifier - http://www.unicode.org/reports/tr35/tr35-31/tr35.html#Unicode_language_identifier
                $matches = array();
                if (preg_match('/^([a-z]{2,3})(?:[_\-]([a-z]{4}))?(?:[_\-]([a-z]{2}|[0-9]{3}))?(?:$|[_\-])/i', $locale, $matches)) {
                    $language = strtolower($matches[1]);
                    $script = isset($matches[2]) ? ucfirst(strtolower($matches[2])) : '';
                    $territory = isset($matches[3]) ? strtoupper($matches[3]) : '';
                    // Likely Subtags - http://www.unicode.org/reports/tr35/tr35-31/tr35.html#Likely_Subtags
                    $variants = array();
                    if (strlen($script) && strlen($territory)) {
                        $variants[] = "{$language}_{$script}_{$territory}";
                    }
                    if (strlen($script)) {
                        $variants[] = "{$language}_{$script}";
                    }
                    if (strlen($territory)) {
                        $variants[] = "{$language}_{$territory}";
                    }
                    $variants[] = $language;
                    foreach ($variants as $variant) {
                        if (array_key_exists($variant, static::$pluralRules)) {
                            $pluralRule = static::$pluralRules[$variant];
                        }
                    }
                }
            }
            $cache[$locale] = $pluralRule;
        }
        if (!strlen($cache[$locale])) {
            throw new \Exception(sprintf('Unrecognized locale: %s', $locale));
        }

        return $cache[$locale];
    }

    /**
     * List of the plural-rules
     * @var array
     * @link http://localization-guide.readthedocs.org/en/latest/l10n/pluralforms.html
     */
    protected static $pluralRules = array(
        'ach' => 'nplurals=2; plural=(n > 1);', // Acholi
        'af' => 'nplurals=2; plural=(n != 1);', // Afrikaans
        'ak' => 'nplurals=2; plural=(n > 1);', // Akan
        'am' => 'nplurals=2; plural=(n > 1);', // Amharic
        'an' => 'nplurals=2; plural=(n != 1);', // Aragonese
        'anp' => 'nplurals=2; plural=(n != 1);', // Angika
        'ar' => 'nplurals=6; plural=(n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5);', // Arabic
        'arn' => 'nplurals=2; plural=(n > 1);', // Mapudungun
        'as' => 'nplurals=2; plural=(n != 1);', // Assamese
        'ast' => 'nplurals=2; plural=(n != 1);', // Asturian
        'ay' => 'nplurals=1; plural=0;', // Aymará
        'az' => 'nplurals=2; plural=(n != 1);', // Azerbaijani
        'be' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', // Belarusian
        'bg' => 'nplurals=2; plural=(n != 1);', // Bulgarian
        'bn' => 'nplurals=2; plural=(n != 1);', // Bengali
        'bo' => 'nplurals=1; plural=0;', // Tibetan
        'br' => 'nplurals=2; plural=(n > 1);', // Breton
        'brx' => 'nplurals=2; plural=(n != 1);', // Bodo
        'bs' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', // Bosnian
        'ca' => 'nplurals=2; plural=(n != 1);', // Catalan
        'cgg' => 'nplurals=1; plural=0;', // Chiga
        'cs' => 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2;', // Czech
        'csb' => 'nplurals=3; plural=(n==1) ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;', // Kashubian
        'cy' => 'nplurals=4; plural=(n==1) ? 0 : (n==2) ? 1 : (n != 8 && n != 11) ? 2 : 3;', // Welsh
        'da' => 'nplurals=2; plural=(n != 1);', // Danish
        'de' => 'nplurals=2; plural=(n != 1);', // German
        'doi' => 'nplurals=2; plural=(n != 1);', // Dogri
        'dz' => 'nplurals=1; plural=0;', // Dzongkha
        'el' => 'nplurals=2; plural=(n != 1);', // Greek
        'en' => 'nplurals=2; plural=(n != 1);', // English
        'eo' => 'nplurals=2; plural=(n != 1);', // Esperanto
        'es' => 'nplurals=2; plural=(n != 1);', // Spanish
        'es_AR' => 'nplurals=2; plural=(n != 1);', // Argentinean Spanish
        'et' => 'nplurals=2; plural=(n != 1);', // Estonian
        'eu' => 'nplurals=2; plural=(n != 1);', // Basque
        'fa' => 'nplurals=1; plural=0;', // Persian
        'ff' => 'nplurals=2; plural=(n != 1);', // Fulah
        'fi' => 'nplurals=2; plural=(n != 1);', // Finnish
        'fil' => 'nplurals=2; plural=(n > 1);', // Filipino
        'fo' => 'nplurals=2; plural=(n != 1);', // Faroese
        'fr' => 'nplurals=2; plural=(n > 1);', // French
        'fur' => 'nplurals=2; plural=(n != 1);', // Friulian
        'fy' => 'nplurals=2; plural=(n != 1);', // Frisian
        'ga' => 'nplurals=5; plural=(n==1) ? 0 : n==2 ? 1 : n<7 ? 2 : n<11 ? 3 : 4;', // Irish
        'gd' => 'nplurals=4; plural=(n==1 || n==11) ? 0 : (n==2 || n==12) ? 1 : (n > 2 && n < 20) ? 2 : 3;', // Scottish Gaelic
        'gl' => 'nplurals=2; plural=(n != 1);', // Galician
        'gu' => 'nplurals=2; plural=(n != 1);', // Gujarati
        'gun' => 'nplurals=2; plural=(n > 1);', // Gun
        'ha' => 'nplurals=2; plural=(n != 1);', // Hausa
        'he' => 'nplurals=2; plural=(n != 1);', // Hebrew
        'hi' => 'nplurals=2; plural=(n != 1);', // Hindi
        'hne' => 'nplurals=2; plural=(n != 1);', // Chhattisgarhi
        'hr' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', // Croatian
        'hu' => 'nplurals=2; plural=(n != 1);', // Hungarian
        'hy' => 'nplurals=2; plural=(n != 1);', // Armenian
        'ia' => 'nplurals=2; plural=(n != 1);', // Interlingua
        'id' => 'nplurals=1; plural=0;', // Indonesian
        'is' => 'nplurals=2; plural=(n%10!=1 || n%100==11);', // Icelandic
        'it' => 'nplurals=2; plural=(n != 1);', // Italian
        'ja' => 'nplurals=1; plural=0;', // Japanese
        'jbo' => 'nplurals=1; plural=0;', // Lojban
        'jv' => 'nplurals=2; plural=(n != 0);', // Javanese
        'ka' => 'nplurals=1; plural=0;', // Georgian
        'kk' => 'nplurals=1; plural=0;', // Kazakh
        'kl' => 'nplurals=2; plural=(n != 1);', // Greenlandic
        'km' => 'nplurals=1; plural=0;', // Khmer
        'kn' => 'nplurals=2; plural=(n != 1);', // Kannada
        'ko' => 'nplurals=1; plural=0;', // Korean
        'ku' => 'nplurals=2; plural=(n != 1);', // Kurdish
        'kw' => 'nplurals=4; plural=(n==1) ? 0 : (n==2) ? 1 : (n == 3) ? 2 : 3;', // Cornish
        'ky' => 'nplurals=1; plural=0;', // Kyrgyz
        'lb' => 'nplurals=2; plural=(n != 1);', // Letzeburgesch
        'ln' => 'nplurals=2; plural=(n > 1);', // Lingala
        'lo' => 'nplurals=1; plural=0;', // Lao
        'lt' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 || n%100>=20) ? 1 : 2);', // Lithuanian
        'lv' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2);', // Latvian
        'mai' => 'nplurals=2; plural=(n != 1);', // Maithili
        'mfe' => 'nplurals=2; plural=(n > 1);', // Mauritian Creole
        'mg' => 'nplurals=2; plural=(n > 1);', // Malagasy
        'mi' => 'nplurals=2; plural=(n > 1);', // Maori
        'mk' => 'nplurals=2; plural= n==1 || n%10==1 ? 0 : 1; Can’t be correct needs a 2 somewhere', // Macedonian
        'ml' => 'nplurals=2; plural=(n != 1);', // Malayalam
        'mn' => 'nplurals=2; plural=(n != 1);', // Mongolian
        'mni' => 'nplurals=2; plural=(n != 1);', // Manipuri
        'mnk' => 'nplurals=3; plural=(n==0 ? 0 : n==1 ? 1 : 2);', // Mandinka
        'mr' => 'nplurals=2; plural=(n != 1);', // Marathi
        'ms' => 'nplurals=1; plural=0;', // Malay
        'mt' => 'nplurals=4; plural=(n==1 ? 0 : n==0 || ( n%100>1 && n%100<11) ? 1 : (n%100>10 && n%100<20 ) ? 2 : 3);', // Maltese
        'my' => 'nplurals=1; plural=0;', // Burmese
        'nah' => 'nplurals=2; plural=(n != 1);', // Nahuatl
        'nap' => 'nplurals=2; plural=(n != 1);', // Neapolitan
        'nb' => 'nplurals=2; plural=(n != 1);', // Norwegian Bokmal
        'ne' => 'nplurals=2; plural=(n != 1);', // Nepali
        'nl' => 'nplurals=2; plural=(n != 1);', // Dutch
        'nn' => 'nplurals=2; plural=(n != 1);', // Norwegian Nynorsk
        'no' => 'nplurals=2; plural=(n != 1);', // Norwegian (old code)
        'nso' => 'nplurals=2; plural=(n != 1);', // Northern Sotho
        'oc' => 'nplurals=2; plural=(n > 1);', // Occitan
        'or' => 'nplurals=2; plural=(n != 1);', // Oriya
        'pa' => 'nplurals=2; plural=(n != 1);', // Punjabi
        'pap' => 'nplurals=2; plural=(n != 1);', // Papiamento
        'pl' => 'nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', // Polish
        'pms' => 'nplurals=2; plural=(n != 1);', // Piemontese
        'ps' => 'nplurals=2; plural=(n != 1);', // Pashto
        'pt' => 'nplurals=2; plural=(n != 1);', // Portuguese
        'pt_BR' => 'nplurals=2; plural=(n > 1);', // Brazilian Portuguese
        'rm' => 'nplurals=2; plural=(n != 1);', // Romansh
        'ro' => 'nplurals=3; plural=(n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2);', // Romanian
        'ru' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', // Russian
        'rw' => 'nplurals=2; plural=(n != 1);', // Kinyarwanda
        'sah' => 'nplurals=1; plural=0;', // Yakut
        'sat' => 'nplurals=2; plural=(n != 1);', // Santali
        'sco' => 'nplurals=2; plural=(n != 1);', // Scots
        'sd' => 'nplurals=2; plural=(n != 1);', // Sindhi
        'se' => 'nplurals=2; plural=(n != 1);', // Northern Sami
        'si' => 'nplurals=2; plural=(n != 1);', // Sinhala
        'sk' => 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2;', // Slovak
        'sl' => 'nplurals=4; plural=(n%100==1 ? 1 : n%100==2 ? 2 : n%100==3 || n%100==4 ? 3 : 0);', // Slovenian
        'so' => 'nplurals=2; plural=(n != 1);', // Somali
        'son' => 'nplurals=2; plural=(n != 1);', // Songhay
        'sq' => 'nplurals=2; plural=(n != 1);', // Albanian
        'sr' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', // Serbian
        'su' => 'nplurals=1; plural=0;', // Sundanese
        'sv' => 'nplurals=2; plural=(n != 1);', // Swedish
        'sw' => 'nplurals=2; plural=(n != 1);', // Swahili
        'ta' => 'nplurals=2; plural=(n != 1);', // Tamil
        'te' => 'nplurals=2; plural=(n != 1);', // Telugu
        'tg' => 'nplurals=2; plural=(n > 1);', // Tajik
        'th' => 'nplurals=1; plural=0;', // Thai
        'ti' => 'nplurals=2; plural=(n > 1);', // Tigrinya
        'tk' => 'nplurals=2; plural=(n != 1);', // Turkmen
        'tr' => 'nplurals=2; plural=(n > 1);', // Turkish
        'tt' => 'nplurals=1; plural=0;', // Tatar
        'ug' => 'nplurals=1; plural=0;', // Uyghur
        'uk' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', // Ukrainian
        'ur' => 'nplurals=2; plural=(n != 1);', // Urdu
        'uz' => 'nplurals=2; plural=(n > 1);', // Uzbek
        'vi' => 'nplurals=1; plural=0;', // Vietnamese
        'wa' => 'nplurals=2; plural=(n > 1);', // Walloon
        'wo' => 'nplurals=1; plural=0;', // Wolof
        'yo' => 'nplurals=2; plural=(n != 1);', // Yoruba
        'zh' => 'nplurals=1; plural=0;', // Chinese
        // 'zh' => 'nplurals=2; plural=(n > 1);', // In rare cases where plural form introduces difference in personal pronoun (such as her vs. they, we vs. I), the plural form is different.
    );
}
