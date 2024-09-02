<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Nette;
use Nette\Utils\Strings;
use Tester\Assert;

final class HtmlAssert
{

    use Nette\StaticClass;

    private const IE_BUG_FIX = '<!--[if IE]><input type=IEbug disabled style="display:none"><![endif]-->';

    public static function matchFile(string $file, string $actual, ?string $description = null): void
    {
        $expected = Nette\Utils\FileSystem::read($file);
        Assert::match(self::normalize($expected), self::normalize($actual), $description);
    }

    private static function normalize(string $content): string
    {
        $content = self::removeIeBugFix($content);
        $content = self::normalizeWhiteSpace($content);
        $content = self::normalizeHtmlAttributes($content);
        $content = self::normalizeFormEnd($content);
        return $content;
    }

    private static function normalizeWhiteSpace(string $content): string
    {
        $content = Strings::normalizeNewLines($content);
        $content = Strings::replace($content, '~^[\t ]+|[\t ]+$~m', ''); // remove leading and trailing whitespace
        $content = Strings::replace($content, "~\n+~", "\n"); // remove empty lines
        return $content;
    }

    private static function normalizeHtmlAttributes(string $content): string
    {
        $content = Strings::replace(
            $content,
            '~(<[^>\s]+)\s*([^>]*?)\s*(/?>)~m',
            function (array $matches): string {
                $attributes = Strings::matchAll($matches[2], '~[^=\s]+(?:=(?:\'[^\']*\'|"[^"]*"))?~', PREG_PATTERN_ORDER)[0];
                $serializedAttributes = '';
                if ($attributes !== []) {
                    sort($attributes);
                    $serializedAttributes = ' ' . implode(' ', $attributes);
                }
                return $matches[1] . $serializedAttributes . $matches[3];
            },
        );
        return $content;
    }

    private static function removeIeBugFix(string $content): string
    {
        return str_replace(self::IE_BUG_FIX, '', $content);
    }

    private static function normalizeFormEnd(string $content): string
    {
        $content = Strings::replace($content, '~>\s*</form>~m', ">\n</form>"); // remove whitespace difference between Latte 2 and Latte 3
        return $content;
    }

}
