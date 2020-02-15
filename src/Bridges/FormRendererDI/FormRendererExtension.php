<?php
declare(strict_types = 1);

namespace Nepada\Bridges\FormRendererDI;

use Nepada\FormRenderer\Bootstrap3Renderer;
use Nepada\FormRenderer\IBootstrap3RendererFactory;
use Nepada\FormRenderer\ITemplateRendererFactory;
use Nepada\FormRenderer\TemplateRenderer;
use Nette;
use Nette\DI\CompilerExtension;

class FormRendererExtension extends CompilerExtension
{

    public function getConfigSchema(): Nette\Schema\Schema
    {
        $imports = Nette\Schema\Expect::listOf('string');

        $default = Nette\Schema\Expect::structure([
            'imports' => (clone $imports)->default([
                TemplateRenderer::DEFAULT_FORM_BLOCKS_TEMPLATE_FILE,
            ]),
        ]);

        $bootstrap3Mode = Nette\Schema\Expect::anyOf(
            Bootstrap3Renderer::MODE_BASIC,
            Bootstrap3Renderer::MODE_INLINE,
            Bootstrap3Renderer::MODE_HORIZONTAL
        )->default(Bootstrap3Renderer::MODE_BASIC);

        $bootstrap3 = Nette\Schema\Expect::structure([
            'imports' => clone $imports,
            'mode' => $bootstrap3Mode,
        ]);

        return Nette\Schema\Expect::structure([
            'default' => $default,
            'bootstrap3' => $bootstrap3,
        ]);
    }

    public function loadConfiguration(): void
    {
        $config = $this->getConfig();
        assert($config instanceof \stdClass);
        $container = $this->getContainerBuilder();

        $defaultRendererFactory = $container->addFactoryDefinition($this->prefix('defaultRendererFactory'))
            ->setImplement(ITemplateRendererFactory::class);

        $defaultRendererFactoryResultDefinition = $defaultRendererFactory->getResultDefinition();
        foreach ($config->default->imports as $templateFile) {
            $defaultRendererFactoryResultDefinition->addSetup('importTemplate', [$templateFile]);
        }

        $bootstrap3RendererFactory = $container->addFactoryDefinition($this->prefix('bootstrap3RendererFactory'))
            ->setImplement(IBootstrap3RendererFactory::class);

        $bootstrap3RendererFactoryResultDefinition = $bootstrap3RendererFactory->getResultDefinition();
        foreach ($config->bootstrap3->imports as $templateFile) {
            $bootstrap3RendererFactoryResultDefinition->addSetup('importTemplate', [$templateFile]);
        }
        if ($config->bootstrap3->mode === Bootstrap3Renderer::MODE_HORIZONTAL) {
            $bootstrap3RendererFactoryResultDefinition->addSetup('setHorizontalMode');
        } elseif ($config->bootstrap3->mode === Bootstrap3Renderer::MODE_INLINE) {
            $bootstrap3RendererFactoryResultDefinition->addSetup('setInlineMode');
        } elseif ($config->bootstrap3->mode === Bootstrap3Renderer::MODE_BASIC) {
            $bootstrap3RendererFactoryResultDefinition->addSetup('setBasicMode');
        } else {
            throw new \InvalidArgumentException("Unsupported bootstrap 3 renderer mode '{$config->bootstrap3->mode}'.");
        }
    }

}
