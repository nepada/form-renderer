<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;


interface IBootstrap3RendererFactory
{

    /**
     * @return Bootstrap3Renderer
     */
    public function create(): Bootstrap3Renderer;

}
