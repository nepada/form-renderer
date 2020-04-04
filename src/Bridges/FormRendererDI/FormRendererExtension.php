<?php
declare(strict_types = 1);

namespace Nepada\Bridges\FormRendererDI;

use Nepada\FormRenderer\Bootstrap3Renderer;
use Nepada\FormRenderer\Bootstrap3RendererFactory;
use Nepada\FormRenderer\Bootstrap4Renderer;
use Nepada\FormRenderer\Bootstrap4RendererFactory;
use Nepada\FormRenderer\Filters\SafeTranslateFilterFactory;
use Nepada\FormRenderer\TemplateRenderer;
use Nepada\FormRenderer\TemplateRendererFactory;
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

        $bootstrap3 = Nette\Schema\Expect::structure([
            'imports' => clone $imports,
            'mode' => Nette\Schema\Expect::anyOf(
                Bootstrap3Renderer::MODE_BASIC,
                Bootstrap3Renderer::MODE_INLINE,
                Bootstrap3Renderer::MODE_HORIZONTAL,
            )->default(Bootstrap3Renderer::MODE_BASIC),
        ]);

        $bootstrap4 = Nette\Schema\Expect::structure([
            'imports' => clone $imports,
            'mode' => Nette\Schema\Expect::anyOf(
                Bootstrap4Renderer::MODE_BASIC,
                Bootstrap4Renderer::MODE_INLINE,
                Bootstrap4Renderer::MODE_HORIZONTAL,
            )->default(Bootstrap4Renderer::MODE_BASIC),
            'useCustomControls' => Nette\Schema\Expect::bool(false),
        ]);

        return Nette\Schema\Expect::structure([
            'default' => $default,
            'bootstrap3' => $bootstrap3,
            'bootstrap4' => $bootstrap4,
        ]);
    }

    public function loadConfiguration(): void
    {
        $config = $this->getConfig();
        assert($config instanceof \stdClass);
        $container = $this->getContainerBuilder();

        $container->addFactoryDefinition($this->prefix('filters.safeTranslateFilterFactory'))
            ->setImplement(SafeTranslateFilterFactory::class);

        $container->addFactoryDefinition($this->prefix('templateRendererFactory'))
            ->setImplement(TemplateRendererFactory::class)
            ->setAutowired(false);

        $this->setupDefaultRenderer($config->default);
        $this->setupBootstrap3Renderer($config->bootstrap3);
        $this->setupBootstrap4Renderer($config->bootstrap4);
    }

    private function setupDefaultRenderer(\stdClass $config): void
    {
        $container = $this->getContainerBuilder();

        $factory = $container->addFactoryDefinition($this->prefix('defaultRendererFactory'))
            ->setImplement(TemplateRendererFactory::class);

        $resultDefinition = $factory->getResultDefinition();
        foreach ($config->imports as $templateFile) {
            $resultDefinition->addSetup('importTemplate', [$templateFile]);
        }
    }

    private function setupBootstrap3Renderer(\stdClass $config): void
    {
        $container = $this->getContainerBuilder();

        $factory = $container->addFactoryDefinition($this->prefix('bootstrap3RendererFactory'))
            ->setImplement(Bootstrap3RendererFactory::class);

        $resultDefinition = $factory->getResultDefinition();
        $resultDefinition->setArguments(['templateRendererFactory' => $this->prefix('@templateRendererFactory')]);
        foreach ($config->imports as $templateFile) {
            $resultDefinition->addSetup('importTemplate', [$templateFile]);
        }
        if ($config->mode === Bootstrap3Renderer::MODE_HORIZONTAL) {
            $resultDefinition->addSetup('setHorizontalMode');
        } elseif ($config->mode === Bootstrap3Renderer::MODE_INLINE) {
            $resultDefinition->addSetup('setInlineMode');
        } elseif ($config->mode === Bootstrap3Renderer::MODE_BASIC) {
            $resultDefinition->addSetup('setBasicMode');
        } else {
            throw new \InvalidArgumentException("Unsupported bootstrap 3 renderer mode '{$config->mode}'.");
        }
    }

    private function setupBootstrap4Renderer(\stdClass $config): void
    {
        $container = $this->getContainerBuilder();

        $factory = $container->addFactoryDefinition($this->prefix('bootstrap4RendererFactory'))
            ->setImplement(Bootstrap4RendererFactory::class);

        $resultDefinition = $factory->getResultDefinition();
        $resultDefinition->setArguments(['templateRendererFactory' => $this->prefix('@templateRendererFactory')]);
        $resultDefinition->addSetup('setUseCustomControls', [$config->useCustomControls]);
        foreach ($config->imports as $templateFile) {
            $resultDefinition->addSetup('importTemplate', [$templateFile]);
        }
        if ($config->mode === Bootstrap4Renderer::MODE_HORIZONTAL) {
            $resultDefinition->addSetup('setHorizontalMode');
        } elseif ($config->mode === Bootstrap4Renderer::MODE_INLINE) {
            $resultDefinition->addSetup('setInlineMode');
        } elseif ($config->mode === Bootstrap4Renderer::MODE_BASIC) {
            $resultDefinition->addSetup('setBasicMode');
        } else {
            throw new \InvalidArgumentException("Unsupported bootstrap 4 renderer mode '{$config->mode}'.");
        }
    }

}
