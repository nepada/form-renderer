<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer\Filters;

use Nette;

interface SafeTranslateFilterFactory
{

    public function create(?Nette\Localization\Translator $translator): SafeTranslateFilter;

}
