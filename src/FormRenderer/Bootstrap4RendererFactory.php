<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

interface Bootstrap4RendererFactory
{

    public function create(): Bootstrap4Renderer;

}
