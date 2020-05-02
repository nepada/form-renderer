<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Nette;
use Tester\Assert;

final class HtmlAssert
{

    use Nette\StaticClass;

    public static function matchFile(string $file, string $actual, ?string $description = null): void
    {
        $expected = Nette\Utils\FileSystem::read($file);
        Assert::match(self::normalizeWhiteSpace($expected), self::normalizeWhiteSpace($actual), $description);
    }

    private static function normalizeWhiteSpace(string $content): string
    {
        $content = Nette\Utils\Strings::normalizeNewLines($content);
        $content = Nette\Utils\Strings::replace($content, '~^[\t ]+|[\t ]+$~m', ''); // remove leading and trailing whitespace
        $content = Nette\Utils\Strings::replace($content, "~\n+~", "\n"); // remove empty lines
        return $content;
    }

}
