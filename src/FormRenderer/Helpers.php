<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

use Nette;
use function is_array;

final class Helpers
{

    use Nette\StaticClass;

    /**
     * @param string|mixed[]|null $value
     * @return string[]
     */
    public static function parseClassList(string|array|null $value): array
    {
        if ($value === null) {
            return [];
        }

        $classes = [];
        $values = is_array($value) ? $value : [$value];
        foreach ($values as $k => $v) {
            if ($v === true) {
                $classes = array_merge($classes, explode(' ', (string) $k));
            } elseif (is_string($v) && $v !== '') {
                $classes = array_merge($classes, explode(' ', $v));
            }
        }

        return $classes;
    }

}
