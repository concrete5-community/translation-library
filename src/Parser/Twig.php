<?php

namespace C5TL\Parser;

use Gettext\Translations;
use Twig\Loader\ArrayLoader;

/**
 * Extract translatable strings from PHP files (functions t(), tc() and t2()).
 */
class Twig extends Php
{
    /** @var \Twig\Environment|null */
    private static $twig = null;

    protected $fileRegex = '/^(.*)\.html.twig$/i';

    /**
     * {@inheritdoc}
     *
     * @see \C5TL\Parser::getParserName()
     */
    public function getParserName()
    {
        return function_exists('t') ? t('Twig Parser') : 'Twig Parser';
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

    public static function isSupported(): bool
    {
        return class_exists(\Twig\Lexer::class);
    }

    protected static function parseDirectoryDoTwig(string $rootDirectory, array $files): \Gettext\Translations
    {
        $translations = new Translations();
        foreach ($files as $file) {
            $pathName = $rootDirectory . '/' . $file;
            $content = file_get_contents($pathName);

            $translations->mergeWith(self::parseTwigString($file, $content));
        }

        return $translations;
    }

    protected static function parseTwigString(string $file, string $source): \Gettext\Translations
    {
        $tokens = self::twig()->tokenize(new \Twig\Source($source, $file));
        $translations = new Translations();
        while (!$tokens->isEOF()) {
            $name = $tokens->next();

            if ($name->getType() !== $name::NAME_TYPE) {
                continue;
            }

            // Name type means it might be a function call, lets check the value and check for an opening parenthesis

            $func = $name->getValue();
            if ($func !== 't' && $func !== 'tc' && $func !== 't2') {
                continue;
            }

            $reference = $name->getLine();
            $paren = $tokens->next();
            if ($paren->getType() !== $name::PUNCTUATION_TYPE || $paren->getValue() !== '(') {
                // this isn't a function call after all
                continue;
            }

            $subject = $tokens->next();
            if ($subject->getType() !== $subject::STRING_TYPE) {
                continue;
            }

            $translation = null;
            switch ($func) {
                case 't':
                    $translation = new \Gettext\Translation('', $subject->getValue(), '');
                    break;

                case 'tc':
                    $context = $subject->getValue();
                    $tokens->next(); // Skip `,`
                    $subject = $tokens->next();
                    if ($subject->getType() !== $subject::STRING_TYPE) {
                        continue 2;
                    }

                    $translation = new \Gettext\Translation($context, $subject->getValue(), '');
                    break;

                case 't2':
                    $singular = $subject->getValue();
                    $tokens->next(); // Skip `,`
                    $plural = $tokens->next();
                    if ($plural->getType() !== $subject::STRING_TYPE) {
                        continue 2;
                    }

                    $translation = new \Gettext\Translation('', $singular, $plural->getValue());
            }

            if (!$translation) {
                continue;
            }

            $translation->addReference($file, $reference);
            $translations->mergeWith(new Translations([$translation]));
        }

        return $translations;
    }

    protected function parseDirectoryFiles(string $rootDirectory, array $phpFiles)
    {
        return self::parseDirectoryDoTwig($rootDirectory, $phpFiles);
    }

    public static function twig(): \Twig\Environment
    {
        if (!self::$twig instanceof \Twig\Environment) {
            self::$twig = new \Twig\Environment(new ArrayLoader([]));
        }

        return self::$twig;
    }
}
