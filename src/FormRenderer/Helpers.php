<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

use Nette;

final class Helpers
{

    use Nette\StaticClass;

    /**
     * @param string|mixed[]|null $value
     * @return string[]
     */
    public static function parseClassList($value): array
    {
        $classes = [];
        foreach ((array) $value as $k => $v) {
            if ($v === true) {
                $classes = array_merge($classes, explode(' ', $k));
            } elseif (is_string($v) && $v !== '') {
                $classes = array_merge($classes, explode(' ', $v));
            }
        }

        return $classes;
    }

}
