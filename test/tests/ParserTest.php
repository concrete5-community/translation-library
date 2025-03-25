<?php

namespace C5TL\Test;

use C5TL\Parser\Php;
use C5TL\Parser\Twig;
use Gettext\Translations;

class ParserTest extends TestCase
{
    /**
     * @dataProvider passTranslations
     */
    public function testPhpParserLocatesAndParses(bool $passTranslations): void
    {
        $parser = new Php();
        $translations = $passTranslations ? new Translations() : null;

        $result = $parser->parseDirectory($this->getTemplateDir(), 'test', $translations);
        if ($passTranslations) {
            $this->assertSame($translations, $result);
        }

        $clean = [];
        /** @var \Gettext\Translation $translation */
        foreach ($result as $translation) {
            $clean[$translation->getOriginal()] = $translation;
        }

        $this->assertEquals([['test/example_view.php', '3']], $clean['foo']->getReferences());
        $this->assertEquals([['test/example_view.php', '4']], $clean['baz']->getReferences());
        $this->assertEquals('bazs', $clean['baz']->getPlural());
        $this->assertEquals([['test/example_view.php', '5']], $clean['bar']->getReferences());
        $this->assertEquals('context', $clean['bar']->getContext());


        $this->assertEquals([
            ['test/example_view.php', '6'],
            ['test/subdir/another_view.php', '3'],
        ], $clean['test']->getReferences());
    }

    /**
     * @dataProvider passTranslations
     */
    public function testTwigParserLocatesAndParses(bool $passTranslations): void
    {
        $parser = new Twig();
        $translations = $passTranslations ? new Translations() : null;
        $result = $parser->parseDirectory($this->getTemplateDir(), 'test', $translations);

        $clean = [];
        /** @var \Gettext\Translation $translation */
        foreach ($result as $translation) {
            $clean[$translation->getOriginal()] = $translation;
        }

        $this->assertEquals([['test/example_view.html.twig', 1]], $clean['foo']->getReferences());
        $this->assertEquals([['test/example_view.html.twig', 2]], $clean['baz']->getReferences());
        $this->assertEquals('bazs', $clean['baz']->getPlural());
        $this->assertEquals([['test/example_view.html.twig', 4]], $clean['bar']->getReferences());
        $this->assertEquals('context', $clean['bar']->getContext());

        $this->assertEquals([
            ['test/example_view.html.twig', 9],
            ['test/subdir/another_view.html.twig', 1],
        ], $clean['test']->getReferences());
    }

    public function passTranslations(): array
    {
        return [
            [true],
            [false],
        ];
    }

    private function getTemplateDir()
    {
        return __DIR__ . '/../fixtures/parser';
    }
}