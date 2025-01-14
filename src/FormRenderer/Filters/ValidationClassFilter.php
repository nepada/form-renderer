<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer\Filters;

use Nette;

final class ValidationClassFilter
{

    use Nette\SmartObject;

    private ?string $invalidClass;

    private ?string $validClass;

    public function __construct(?string $invalidClass, ?string $validClass)
    {
        $this->invalidClass = $invalidClass;
        $this->validClass = $validClass;
    }

    public function __invoke(Nette\Forms\Control $control): ?string
    {
        if (count($control->getErrors()) > 0) {
            return $this->invalidClass;
        }

        if ($this->isFilled($control)) {
            return $this->validClass;
        }

        return null;
    }

    private function isFilled(Nette\Forms\Control $control): bool
    {
        if ($control instanceof Nette\Forms\Controls\TextInput && $control->getControlPrototype()->type === 'password') {
            return false;
        }

        if ($control instanceof Nette\Forms\Controls\BaseControl || method_exists($control, 'isFilled')) {
            return (bool) $control->isFilled();
        }

        $value = $control->getValue();
        return $value !== null && $value !== [] && $value !== '';
    }

}
