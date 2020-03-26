<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

interface IBootstrap4RendererFactory
{

    public function create(): Bootstrap4Renderer;

}
