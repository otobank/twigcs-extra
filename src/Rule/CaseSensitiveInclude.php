<?php declare(strict_types=1);

namespace Otobank\TwigcsExtra\Rule;

use FriendsOfTwig\Twigcs\Rule\AbstractRule;
use FriendsOfTwig\Twigcs\Rule\RuleInterface;
use FriendsOfTwig\Twigcs\TwigPort\Token;
use FriendsOfTwig\Twigcs\TwigPort\TokenStream;

final class CaseSensitiveInclude extends AbstractRule implements RuleInterface
{
    /**
     * @var array<string>
     */
    private $paths;

    /**
     * @var array
     */
    private $globPaths;

    public function __construct(int $severity, array $paths)
    {
        parent::__construct($severity);

        // todo validate paths are absolute dirs
        $this->paths = $paths;
    }

    /**
     * {@inheritdoc}
     */
    public function check(TokenStream $tokens)
    {
        $violations = [];

        do {
            $token = $tokens->getCurrent();
            if (!(Token::NAME_TYPE === $token->getType() &&
                in_array($token->getValue(), ['include', 'extends', 'import'], true))) {
                continue;
            }

            $include = $tokens->look(2)->getValue();

            if (!$this->exists($include)) {
                $violations[] = $this->createViolation(
                    $tokens->getSourceContext()->getPath(),
                    $token->getLine(),
                    $token->getColumn(),
                    sprintf('%s "%s" is not valid path.', $token->getValue(), $include)
                );
            }

        } while (!$tokens->isEOF() && $tokens->next());

        return $violations;
    }

    /**
     * @psalm-return list<string>
     */
    public static function globPaths(array $paths) : array
    {
        $pathNames = [];
        foreach (self::recursive($paths) as $i => $globDir) {
            foreach ($globDir as $file) {
                $pathNames[] = $file->getPathname();
            }
        }

        return $pathNames;
    }

    /**
     * @return array<iterable<\SplFileInfo>>
     */
    private static function recursive(array $paths) : array
    {
        $globs = [];

        foreach ($paths as $i => $path) {
            $globs[$i] = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::LEAVES_ONLY);
        }

        return $globs;
    }

    private function exists(string $include) : bool
    {
        foreach ($this->paths as $i => $absoluteDir) {
            $absolutePath = $absoluteDir . DIRECTORY_SEPARATOR . $include;
            if (in_array($absolutePath, $this->getGlobPaths(), true)) {
                return true;
            }
        }

        return false;
    }

    private function getGlobPaths() : array
    {
        if (!$this->globPaths) {
            $this->globPaths = self::globPaths($this->paths);
        }

        return $this->globPaths;
    }
}