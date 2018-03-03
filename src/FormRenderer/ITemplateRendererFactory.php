<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

interface ITemplateRendererFactory
{

    public function create(): TemplateRenderer;

}
