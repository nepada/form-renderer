<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

interface Bootstrap5RendererFactory
{

    public function create(): Bootstrap5Renderer;

}
