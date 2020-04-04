<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

interface TemplateRendererFactory
{

    public function create(): TemplateRenderer;

}
