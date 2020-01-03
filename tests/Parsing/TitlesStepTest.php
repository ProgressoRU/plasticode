<?php

namespace Plasticode\Tests\Parsing;

use PHPUnit\Framework\TestCase;
use Plasticode\Config\Parsing as ParsingConfig;
use Plasticode\Core\Renderer;
use Plasticode\Parsing\Parser;
use Plasticode\Parsing\ParsingContext;
use Plasticode\Parsing\Steps\TitlesStep;
use Slim\Views\Twig;

final class TitlesStepTest extends TestCase
{
    /**
     * @covers TitlesStep
     */
    public function testParse() : void
    {
        $view = new Twig('views/bootstrap3/', ['debug' => true]);

        $renderer = new Renderer($view);

        $config = new ParsingConfig();
        $lineParser = new Parser($config, $renderer); // dummy parser for now

        $step = new TitlesStep($renderer, $lineParser);

        $lines = [
            '## Title',
            '[b]Hello[/b]',
            '## Some more',
            'Yay text lol'
        ];
        
        $context = ParsingContext::fromLines($lines);
        $context = $step->parse($context);

        $resultLines = $context->getLines();

        $this->assertEquals(count($lines), count($resultLines));

        $this->assertEquals(
            [
                '<p class="subtitle subtitle1" id="1">Title</p>',
                '[b]Hello[/b]',
                '<p class="subtitle subtitle1" id="2">Some more</p>',
                'Yay text lol'
            ],
            $resultLines
        );

        $this->assertCount(2, $context->contents);

        $contents1 = $context->contents[0];
        $contents2 = $context->contents[1];

        $this->assertEquals(
            [1, '1', 'Title'],
            [
                $contents1->level,
                $contents1->label,
                $contents1->text
            ]
        );

        $this->assertEquals(
            [1, '2', 'Some more'],
            [
                $contents2->level,
                $contents2->label,
                $contents2->text
            ]
        );

        $this->assertEmpty($context->largeImages);
        $this->assertEmpty($context->images);
        $this->assertEmpty($context->videos);

        $this->assertNull($context->largeImage());
        $this->assertNull($context->image());
        $this->assertNull($context->video());

        $this->assertNull($context->updatedAt);
    }
}