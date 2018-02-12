<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;


interface ITemplateRendererFactory
{

    /**
     * @return TemplateRenderer
     */
    public function create(): TemplateRenderer;

}
