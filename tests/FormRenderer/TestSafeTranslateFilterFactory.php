<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Nepada\FormRenderer\Filters\SafeTranslateFilter;
use Nepada\FormRenderer\Filters\SafeTranslateFilterFactory;
use Nette;

final class TestSafeTranslateFilterFactory implements SafeTranslateFilterFactory
{

    use Nette\SmartObject;

    public function create(?Nette\Localization\ITranslator $translator): SafeTranslateFilter
    {
        return new SafeTranslateFilter($translator);
    }

}
