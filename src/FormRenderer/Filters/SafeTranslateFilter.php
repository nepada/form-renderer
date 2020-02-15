<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer\Filters;

use Latte;
use Nette;

final class SafeTranslateFilter
{

    use Nette\SmartObject;

    private ?Nette\Localization\ITranslator $translator;

    public function __construct(?Nette\Localization\ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Latte\Runtime\FilterInfo $filterInfo
     * @param mixed ...$args
     * @return mixed
     */
    public function __invoke(Latte\Runtime\FilterInfo $filterInfo, ...$args)
    {
        if ($this->translator === null) {
            return count($args) > 0 ? reset($args) : null;
        }

        if (count($args) === 1 && $this->isHtmlString($args[0])) {
            return $args[0];
        }

        return $this->translator->translate(...$args);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function isHtmlString($value): bool
    {
        if ($value instanceof Nette\Utils\IHtmlString) {
            // nette/utils interface
            return true;
        }

        if ($value instanceof Latte\Runtime\IHtmlString) {
            // latte/latte interface
            return true;
        }

        return false;
    }

}
