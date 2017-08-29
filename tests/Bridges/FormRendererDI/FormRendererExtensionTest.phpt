<?php
/**
 * Test: Nepada\Bridges\FormRendererDI\FormRendererExtensionTest.
 *
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace NepadaTests\Bridges\FormRendererDI;

use Nepada;
use Nepada\FormRenderer;
use NepadaTests\TestCase;
use Nette;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class FormRendererExtensionTest extends TestCase
{

    /** @var Nette\DI\Container */
    private $container;


    public function testServices(): void
    {
        Assert::type(FormRenderer\ITemplateRendererFactory::class, $this->container->getService('formRenderer.defaultRendererFactory'));
        Assert::type(FormRenderer\IBootstrap3RendererFactory::class, $this->container->getService('formRenderer.bootstrap3RendererFactory'));
    }

    public function testRendering(): void
    {
        $form = new Nette\Forms\Form();

        $defaultRenderer = $this->container->getByType(FormRenderer\ITemplateRendererFactory::class)->create();
        Assert::same('FORM', $defaultRenderer->render($form));

        $bootstrap3Renderer = $this->container->getByType(FormRenderer\IBootstrap3RendererFactory::class)->create();
        Assert::same('FORM horizontal', $bootstrap3Renderer->render($form));
    }

    protected function setUp(): void
    {
        $configurator = new Nette\Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->setDebugMode(true);
        $configurator->addParameters(['fixturesDir' => __DIR__ . '/fixtures']);
        $configurator->addConfig(__DIR__ . '/fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

}


\run(new FormRendererExtensionTest());
