<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer\Filters\Fixtures;

use Nette;

class CustomControl implements Nette\Forms\Control
{

    use Nette\SmartObject;

    private mixed $value;

    /**
     * @var string[]
     */
    private array $errors;

    /**
     * @param mixed $value
     * @param string[] $errors
     */
    public function __construct(mixed $value = null, array $errors = [])
    {
        $this->value = $value;
        $this->errors = $errors;
    }

    /**
     * @param mixed $value
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
     * @return string[]
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
