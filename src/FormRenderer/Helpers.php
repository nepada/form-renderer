<?php
/**
 * This file is part of the nepada/form-renderer.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

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
            if ($v != null) { // intentionally ==, skip NULLs & empty string
                $classes = array_merge($classes, explode(' ', $v === true ? $k : $v));
            }
        }

        return $classes;
    }

}
