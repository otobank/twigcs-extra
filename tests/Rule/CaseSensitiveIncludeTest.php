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
            'success' => [
                $lexer->tokenize(
                    new Source(
                        '{%include "my/includes/TPL.html" %}',
                        'my/path/file.html.twig',
                        ltrim(str_replace(getcwd(), '', 'my/path/file.html.twig'), '/')
                    )
                ),
                0
            ],
            'case insensitive' => [
                $lexer->tokenize(
                    new Source(
                        '{%include "my/includes/tpl.html" %}',
                        'my/path/file.html.twig',
                        ltrim(str_replace(getcwd(), '', 'my/path/file.html.twig'), '/')
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
