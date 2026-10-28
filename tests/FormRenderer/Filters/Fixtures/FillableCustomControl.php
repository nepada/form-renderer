<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer\Filters\Fixtures;

class FillableCustomControl extends CustomControl
{

    /**
     * @param list<string|\Stringable> $errors
     */
    public function __construct(
        private bool $isFilled,
        mixed $value = null,
        array $errors = [],
    )
    {
        parent::__construct($value, $errors);
    }

    public function isFilled(): bool
    {
        return $this->isFilled;
    }

}
