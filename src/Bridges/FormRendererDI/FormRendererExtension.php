<?php
declare(strict_types = 1);

namespace Nepada\Bridges\FormRendererDI;

use Nepada\FormRenderer\Bootstrap3Renderer;
use Nepada\FormRenderer\IBootstrap3RendererFactory;
use Nepada\FormRenderer\ITemplateRendererFactory;
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

        if (method_exists($container, 'addFactoryDefinition')) { // Nette 3.0
            $defaultRendererFactory = $container->addFactoryDefinition($this->prefix('defaultRendererFactory'));
            $defaultRendererFactoryResultDefinition = $defaultRendererFactory->getResultDefinition();
            $bootstrap3RendererFactory = $container->addFactoryDefinition($this->prefix('bootstrap3RendererFactory'));
            $bootstrap3RendererFactoryResultDefinition = $bootstrap3RendererFactory->getResultDefinition();
        } else { // Nette 2.4 BC
            $defaultRendererFactory = $container->addDefinition($this->prefix('defaultRendererFactory'));
            $defaultRendererFactoryResultDefinition = $defaultRendererFactory;
            $bootstrap3RendererFactory = $container->addDefinition($this->prefix('bootstrap3RendererFactory'));
            $bootstrap3RendererFactoryResultDefinition = $bootstrap3RendererFactory;
        }

        $defaultRendererFactory->setImplement(ITemplateRendererFactory::class);
        foreach ($config['default']['imports'] as $templateFile) {
            $defaultRendererFactoryResultDefinition->addSetup('importTemplate', [$templateFile]);
        }

        $bootstrap3RendererFactory->setImplement(IBootstrap3RendererFactory::class);
        foreach ($config['bootstrap3']['imports'] as $templateFile) {
            $bootstrap3RendererFactoryResultDefinition->addSetup('importTemplate', [$templateFile]);
        }
        if ($config['bootstrap3']['mode'] === Bootstrap3Renderer::MODE_HORIZONTAL) {
            $bootstrap3RendererFactoryResultDefinition->addSetup('setHorizontalMode');
        } elseif ($config['bootstrap3']['mode'] === Bootstrap3Renderer::MODE_INLINE) {
            $bootstrap3RendererFactoryResultDefinition->addSetup('setInlineMode');
        } elseif ($config['bootstrap3']['mode'] === Bootstrap3Renderer::MODE_BASIC) {
            $bootstrap3RendererFactoryResultDefinition->addSetup('setBasicMode');
        } else {
            throw new \InvalidArgumentException("Unsupported bootstrap 3 renderer mode '{$config['bootstrap3']['mode']}'.");
        }
    }

}
