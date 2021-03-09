<?php declare(strict_types=1);

namespace OtobankTest\TwigcsExtra\Rule;

use FriendsOfTwig\Twigcs\Lexer;
use FriendsOfTwig\Twigcs\TwigPort\Source;
use FriendsOfTwig\Twigcs\TwigPort\TokenStream;
use FriendsOfTwig\Twigcs\Validator\Violation;
use Otobank\TwigcsExtra\Rule\CaseSensitiveInclude;
use PHPUnit\Framework\TestCase;

class CaseSensitiveIncludeTest extends TestCase
{
    /**
     * @psalm-return array<string, array<TokenStream, int>>
     */
    public function provideSourcePatterns() : array
    {
        $lexer = new Lexer();

        return [
            'success:include' => [
                $lexer->tokenize(
                    new Source(
                        '{% include "my/includes/TPL.html" %}',
                        'my/path/file.html.twig',
                        'my/path/file.html.twig'
                    )
                ),
                0
            ],
            'success:extends' => [
                $lexer->tokenize(
                    new Source(
                        '{% extends "my/includes/TPL.html" %}',
                        'my/path/file.html.twig',
                        'my/path/file.html.twig'
                    )
                ),
                0
            ],
            'success:import' => [
                $lexer->tokenize(
                    new Source(
                        '{% import "my/includes/TPL.html" as foo %}',
                        'my/path/file.html.twig',
                        'my/path/file.html.twig'
                    )
                ),
                0
            ],
            'case_insensitive:include' => [
                $lexer->tokenize(
                    new Source(
                        '{% include "my/includes/tpl.html" %}',
                        'my/path/file.html.twig',
                        'my/path/file.html.twig'
                    )
                ),
                1
            ],
            'case_insensitive:extends' => [
                $lexer->tokenize(
                    new Source(
                        '{% extends "my/includes/tpl.html" %}',
                        'my/path/file.html.twig',
                        'my/path/file.html.twig'
                    )
                ),
                1
            ],
            'case_insensitive:import' => [
                $lexer->tokenize(
                    new Source(
                        '{% import "my/includes/tpl.html" as foo %}',
                        'my/path/file.html.twig',
                        'my/path/file.html.twig'
                    )
                ),
                1
            ],
        ];
    }

    /**
     * @dataProvider provideSourcePatterns
     */
    public function testCheckWithoutFunctions(TokenStream $tokenStream, int $expectedCount) : void
    {
        $rule = new CaseSensitiveInclude(Violation::SEVERITY_WARNING, [dirname(__DIR__, 1) . '/Resources']);
        $violations = $rule->check($tokenStream);

        $this->assertCount($expectedCount, $violations);
    }
}
