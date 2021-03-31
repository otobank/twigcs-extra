# Twigcs Extra Rules

[Twigcs](https://github.com/friendsoftwig/twigcs) Extra Rules

## Rule

### CaseSensitiveInclude
* checks 'include', 'extends', 'import' path to be valid with case-sensitive.

## Install
`TBD`

## Setup

Adding Extra Rule to your RuleSet

```php
<?php declare(strict_types=1);

use FriendsOfTwig\Twigcs\RegEngine\RulesetBuilder;
use FriendsOfTwig\Twigcs\RegEngine\RulesetConfigurator;
use FriendsOfTwig\Twigcs\Rule;
use FriendsOfTwig\Twigcs\Ruleset\RulesetInterface;
use FriendsOfTwig\Twigcs\Validator\Violation;

class CustomRuleSet implements RulesetInterface
{
    private $twigMajorVersion;

    public function __construct(int $twigMajorVersion)
    {
        $this->twigMajorVersion = $twigMajorVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getRules()
    {
        $configurator = new RulesetConfigurator();
        $configurator->setTwigMajorVersion($this->twigMajorVersion);
        $builder = new RulesetBuilder($configurator);

        return [
            new Rule\LowerCaseVariable(Violation::SEVERITY_ERROR),
            new Rule\RegEngineRule(Violation::SEVERITY_ERROR, $builder->build()),
            new Rule\TrailingSpace(Violation::SEVERITY_ERROR),
            new Rule\UnusedMacro(Violation::SEVERITY_WARNING),
            new Rule\UnusedVariable(Violation::SEVERITY_WARNING),
            new \Otobank\TwigcsExtra\Rule\CaseSensitiveInclude(Violation::SEVERITY_ERROR, [__DIR__ . '/templates']),
        ];
    }
}
```

## License

Licensed under the MIT License - see the [LICENSE](LICENSE) file for details

----

OTOBANK Inc.
