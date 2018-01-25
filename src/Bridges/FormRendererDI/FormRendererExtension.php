<?php
/**
 * This file is part of the nepada/form-renderer.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace Nepada\Bridges\FormRendererDI;

use Nepada\FormRenderer\Bootstrap3Renderer;
use Nepada\FormRenderer\IBootstrap3RendererFactory;
use Nepada\FormRenderer\ITemplateRendererFactory;
use Nepada\FormRenderer\InvalidStateException;
use Nepada\FormRenderer\TemplateRenderer;
use Nette\DI\CompilerExtension;


class FormRendererExtension extends CompilerExtension
{

    /** @var mixed[] */
    public $defaults = [
        'default' => [
            'imports' => [
                TemplateRenderer::DEFAULT_FORM_BLOCKS_TEMPLATE_FILE,
            ],
        ],
        'bootstrap3' => [
            'mode' => Bootstrap3Renderer::MODE_BASIC,
            'imports' => [],
        ],
    ];


    public function loadConfiguration(): void
    {
        $config = $this->validateConfig($this->defaults);
        $container = $this->getContainerBuilder();

        $defaultRendererFactory = $container->addDefinition($this->prefix('defaultRendererFactory'))
            ->setImplement(ITemplateRendererFactory::class);
        foreach ($config['default']['imports'] as $templateFile) {
            $defaultRendererFactory->addSetup('importTemplate', [$templateFile]);
        }

        $bootstrap3RendererFactory = $container->addDefinition($this->prefix('bootstrap3RendererFactory'))
            ->setImplement(IBootstrap3RendererFactory::class);
        foreach ($config['bootstrap3']['imports'] as $templateFile) {
            $bootstrap3RendererFactory->addSetup('importTemplate', [$templateFile]);
        }
        if ($config['bootstrap3']['mode'] === Bootstrap3Renderer::MODE_HORIZONTAL) {
            $bootstrap3RendererFactory->addSetup('setHorizontalMode');
        } elseif ($config['bootstrap3']['mode'] === Bootstrap3Renderer::MODE_INLINE) {
            $bootstrap3RendererFactory->addSetup('setInlineMode');
        } elseif ($config['bootstrap3']['mode'] === Bootstrap3Renderer::MODE_BASIC) {
            $bootstrap3RendererFactory->addSetup('setBasicMode');
        } else {
            throw new InvalidStateException("Unsupported bootstrap 3 renderer mode '{$config['bootstrap3']['mode']}'.");
        }
    }

}
