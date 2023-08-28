<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\FormRendererDI;

use Latte\Engine;
use Nepada\FormRenderer;
use NepadaTests\Environment;
use NepadaTests\TestCase;
use Nette;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class FormRendererLatteStrictExtensionTest extends TestCase
{

    private Nette\DI\Container $container;

    public function testRendering(): void
    {
        $form = new Nette\Forms\Form();

        $defaultRenderer = $this->container->getByType(FormRenderer\TemplateRendererFactory::class)->create();
        Assert::contains('</form>', $defaultRenderer->render($form));

        $bootstrap3Renderer = $this->container->getByType(FormRenderer\Bootstrap3RendererFactory::class)->create();
        Assert::contains('</form>', $bootstrap3Renderer->render($form));

        $bootstrap4Renderer = $this->container->getByType(FormRenderer\Bootstrap4RendererFactory::class)->create();
        Assert::contains('</form>', $bootstrap4Renderer->render($form));
    }

    protected function setUp(): void
    {
        if (Engine::VERSION_ID < 3_00_08) {
            $this->skip('Requires Latte >=3.0.8');
        }
        $configurator = new Nette\Configurator();
        $configurator->setTempDirectory(Environment::getTempDir());
        $configurator->setDebugMode(true);
        $configurator->addParameters(['fixturesDir' => __DIR__ . '/fixtures']);
        $configurator->addConfig(__DIR__ . '/fixtures/configLatteStrict.neon');
        $this->container = $configurator->createContainer();
    }

}


(new FormRendererLatteStrictExtensionTest())->run();
