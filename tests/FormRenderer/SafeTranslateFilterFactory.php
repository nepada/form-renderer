<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Nepada\FormRenderer\Filters\ISafeTranslateFilterFactory;
use Nepada\FormRenderer\Filters\SafeTranslateFilter;
use Nette;

final class SafeTranslateFilterFactory implements ISafeTranslateFilterFactory
{

    use Nette\SmartObject;

    public function create(?Nette\Localization\ITranslator $translator): SafeTranslateFilter
    {
        return new SafeTranslateFilter($translator);
    }

}
