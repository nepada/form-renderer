<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer\Filters\Fixtures;

use Nette;

class CustomControl implements Nette\Forms\Control
{

    use Nette\SmartObject;

    /**
     * @param list<string|\Stringable> $errors
     */
    public function __construct(
        private mixed $value = null,
        private array $errors = [],
    )
    {
    }

    /**
     * @return static
     */
    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function validate(): void
    {
    }

    /**
     * @return list<string|\Stringable>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isOmitted(): bool
    {
        return false;
    }

}
