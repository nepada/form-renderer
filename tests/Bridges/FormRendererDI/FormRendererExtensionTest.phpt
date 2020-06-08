<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\FormRendererDI;

use Nepada\FormRenderer;
use NepadaTests\Environment;
use NepadaTests\TestCase;
use Nette;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class FormRendererExtensionTest extends TestCase
{

    private Nette\DI\Container $container;

    public function testServices(): void
    {
        Assert::type(FormRenderer\Filters\SafeTranslateFilterFactory::class, $this->container->getService('formRenderer.filters.safeTranslateFilterFactory'));
        Assert::type(FormRenderer\TemplateRendererFactory::class, $this->container->getService('formRenderer.templateRendererFactory'));
        Assert::type(FormRenderer\TemplateRendererFactory::class, $this->container->getService('formRenderer.defaultRendererFactory'));
        Assert::type(FormRenderer\Bootstrap3RendererFactory::class, $this->container->getService('formRenderer.bootstrap3RendererFactory'));
        Assert::type(FormRenderer\Bootstrap4RendererFactory::class, $this->container->getService('formRenderer.bootstrap4RendererFactory'));
    }

    public function testRendering(): void
    {
        $form = new Nette\Forms\Form();

        $defaultRenderer = $this->container->getByType(FormRenderer\TemplateRendererFactory::class)->create();
        Assert::same("FORM\n\nform.latte,default.latte\n", $defaultRenderer->render($form));

        $bootstrap3Renderer = $this->container->getByType(FormRenderer\Bootstrap3RendererFactory::class)->create();
        Assert::same("FORM\nhorizontal\nform.latte,bootstrap3.latte\n", $bootstrap3Renderer->render($form));

        $bootstrap4Renderer = $this->container->getByType(FormRenderer\Bootstrap4RendererFactory::class)->create();
        Assert::same("FORM\nhorizontal\nform.latte,bootstrap4.latte\n", $bootstrap4Renderer->render($form));
    }

    protected function setUp(): void
    {
        $configurator = new Nette\Configurator();
        $configurator->setTempDirectory(Environment::getTempDir());
        $configurator->setDebugMode(true);
        $configurator->addParameters(['fixturesDir' => __DIR__ . '/fixtures']);
        $configurator->addConfig(__DIR__ . '/fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

}


(new FormRendererExtensionTest())->run();
