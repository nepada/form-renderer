<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer\Filters;

use Nette;

interface ISafeTranslateFilterFactory
{

    public function create(?Nette\Localization\ITranslator $translator): SafeTranslateFilter;

}
