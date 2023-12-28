<?php
declare(strict_types = 1);

namespace Nepada\Bridges\FormRendererDI;

use Nepada\FormRenderer\Bootstrap3Renderer;
use Nepada\FormRenderer\Bootstrap3RendererFactory;
use Nepada\FormRenderer\Bootstrap4Renderer;
use Nepada\FormRenderer\Bootstrap4RendererFactory;
use Nepada\FormRenderer\Bootstrap5Renderer;
use Nepada\FormRenderer\Bootstrap5RendererFactory;
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
            'renderValidState' => Nette\Schema\Expect::bool(false),
        ]);

        $bootstrap4 = Nette\Schema\Expect::structure([
            'imports' => clone $imports,
            'mode' => Nette\Schema\Expect::anyOf(
                Bootstrap4Renderer::MODE_BASIC,
                Bootstrap4Renderer::MODE_INLINE,
                Bootstrap4Renderer::MODE_HORIZONTAL,
            )->default(Bootstrap4Renderer::MODE_BASIC),
            'useCustomControls' => Nette\Schema\Expect::bool(false),
            'renderValidState' => Nette\Schema\Expect::bool(false),
            'useErrorTooltips' => Nette\Schema\Expect::bool(false),
        ]);

        $bootstrap5 = Nette\Schema\Expect::structure([
            'imports' => clone $imports,
            'mode' => Nette\Schema\Expect::anyOf(
                Bootstrap5Renderer::MODE_BASIC,
                Bootstrap5Renderer::MODE_INLINE,
                Bootstrap5Renderer::MODE_HORIZONTAL,
            )->default(Bootstrap5Renderer::MODE_BASIC),
            'renderValidState' => Nette\Schema\Expect::bool(false),
            'useErrorTooltips' => Nette\Schema\Expect::bool(false),
        ]);

        return Nette\Schema\Expect::structure([
            'default' => $default,
            'bootstrap3' => $bootstrap3,
            'bootstrap4' => $bootstrap4,
            'bootstrap5' => $bootstrap5,
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
        $this->setupBootstrap5Renderer($config->bootstrap5);
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
        $resultDefinition->addSetup('setRenderValidState', [$config->renderValidState]);
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
        $resultDefinition->addSetup('setRenderValidState', [$config->renderValidState]);
        $resultDefinition->addSetup('setUseErrorTooltips', [$config->useErrorTooltips]);
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

    private function setupBootstrap5Renderer(\stdClass $config): void
    {
        $container = $this->getContainerBuilder();

        $factory = $container->addFactoryDefinition($this->prefix('bootstrap5RendererFactory'))
            ->setImplement(Bootstrap5RendererFactory::class);

        $resultDefinition = $factory->getResultDefinition();
        $resultDefinition->setArguments(['templateRendererFactory' => $this->prefix('@templateRendererFactory')]);
        $resultDefinition->addSetup('setRenderValidState', [$config->renderValidState]);
        $resultDefinition->addSetup('setUseErrorTooltips', [$config->useErrorTooltips]);
        foreach ($config->imports as $templateFile) {
            $resultDefinition->addSetup('importTemplate', [$templateFile]);
        }
        if ($config->mode === Bootstrap5Renderer::MODE_HORIZONTAL) {
            $resultDefinition->addSetup('setHorizontalMode');
        } elseif ($config->mode === Bootstrap5Renderer::MODE_INLINE) {
            $resultDefinition->addSetup('setInlineMode');
        } elseif ($config->mode === Bootstrap5Renderer::MODE_BASIC) {
            $resultDefinition->addSetup('setBasicMode');
        } else {
            throw new \InvalidArgumentException("Unsupported bootstrap 5 renderer mode '{$config->mode}'.");
        }
    }

}
