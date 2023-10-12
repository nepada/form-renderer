<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer\Filters;

use Latte;
use Nette;

final class SafeTranslateFilter
{

    use Nette\SmartObject;

    private ?Nette\Localization\Translator $translator;

    public function __construct(?Nette\Localization\Translator $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke(Latte\Runtime\FilterInfo $filterInfo, mixed ...$args): mixed
    {
        if ($this->translator === null) {
            return count($args) > 0 ? reset($args) : null;
        }

        if (count($args) === 1 && $this->isHtmlString($args[0])) {
            return $args[0];
        }

        return $this->translator->translate(...$args);
    }

    private function isHtmlString(mixed $value): bool
    {
        if ($value instanceof Nette\HtmlStringable) {
            // nette/utils interface
            return true;
        }

        if ($value instanceof Latte\Runtime\HtmlStringable) {
            // latte/latte interface
            return true;
        }

        return false;
    }

}
