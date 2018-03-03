<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

interface IBootstrap3RendererFactory
{

    public function create(): Bootstrap3Renderer;

}
