<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer\Filters\Fixtures;

class FillableCustomControl extends CustomControl
{

    private bool $isFilled;

    /**
     * @param bool $isFilled
     * @param mixed $value
     * @param string[] $errors
     */
    public function __construct(bool $isFilled, mixed $value = null, array $errors = [])
    {
        parent::__construct($value, $errors);
        $this->isFilled = $isFilled;
    }

    public function isFilled(): bool
    {
        return $this->isFilled;
    }

}
