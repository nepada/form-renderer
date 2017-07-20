<?php
/**
 * This file is part of the nepada/form-renderer.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace Nepada\FormRenderer;


interface ITemplateRendererFactory
{

    /**
     * @return TemplateRenderer
     */
    public function create(): TemplateRenderer;

}
