<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Mockery;
use Nepada\FormRenderer;
use NepadaTests\TestCase;
use Nette;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class TemplateRendererTest extends TestCase
{

    private TestTemplateRendererFactory $templateRendererFactory;

    private TestFormFactory $testFormFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateRendererFactory = new TestTemplateRendererFactory();
        $this->testFormFactory = new TestFormFactory();
    }

    public function testUnsupportedTemplateType(): void
    {
        $template = Mockery::mock(Nette\Application\UI\Template::class);

        $templateFactory = Mockery::mock(Nette\Application\UI\TemplateFactory::class);
        $templateFactory->shouldReceive('createTemplate')->andReturn($template);

        $renderer = new FormRenderer\TemplateRenderer($templateFactory, new TestSafeTranslateFilterFactory());
        Assert::exception(
            function () use ($renderer): void {
                $renderer->getTemplate();
            },
            \LogicException::class,
            'Template factory returned unsupported template type %a%, only Nette\Bridges\ApplicationLatte\Template is supported.',
        );
    }

    public function testSimple(): void
    {
        $form = $this->testFormFactory->create();

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . '/expected/default.html', $form->__toString());
    }

    public function testErrors(): void
    {
        $form = $this->testFormFactory->create();
        $form->addError('Form error 1.');
        $form->addError('Form error 2.');
        /** @var Nette\Forms\Controls\BaseControl $control */
        foreach ($form->getControls() as $control) {
            if ($control instanceof Nette\Forms\Controls\Button) {
                continue;
            }
            $control->addError("Control {$control->getName()} error 1.");
            $control->addError("Control {$control->getName()} error 2.");
        }

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . '/expected/default-errors.html', $form->__toString());
    }

    public function testRequiredControl(): void
    {
        $form = $this->testFormFactory->create();
        /** @var Nette\Forms\Controls\BaseControl $control */
        foreach ($form->getControls() as $control) {
            $control->setRequired('REQUIRED');
        }

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . '/expected/default-requiredControl.html', $form->__toString());
    }

    public function testControlDescription(): void
    {
        $form = $this->testFormFactory->create();
        /** @var Nette\Forms\Controls\BaseControl $control */
        foreach ($form->getControls() as $control) {
            $control->setOption('description', "Control {$control->getName()} description.");
        }

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . '/expected/default-controlDescription.html', $form->__toString());
    }

    public function testCustomControlId(): void
    {
        $form = $this->testFormFactory->create();
        /** @var Nette\Forms\Controls\BaseControl $control */
        foreach ($form->getControls() as $control) {
            $control->setOption('id', sprintf('custom-%s', $control->lookupPath()));
        }

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . '/expected/default-customControlId.html', $form->__toString());
    }

    public function testCustomControlClass(): void
    {
        $form = $this->testFormFactory->create();
        /** @var Nette\Forms\Controls\BaseControl $control */
        foreach ($form->getControls() as $control) {
            $control->setOption('class', sprintf('custom-%s', $control->lookupPath()));
        }

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . '/expected/default-customControlClass.html', $form->__toString());
    }

    public function testTemplateImports(): void
    {
        $form = $this->testFormFactory->create();

        $renderer = $this->createRenderer();
        $renderer->importTemplate(__DIR__ . '/Fixtures/customPair.latte');
        $renderer->importTemplate(__DIR__ . '/Fixtures/customPairType.latte');
        $renderer->importTemplate(__DIR__ . '/Fixtures/customControl.latte');
        $renderer->importTemplate(__DIR__ . '/Fixtures/customControlType.latte');
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . '/expected/default-imports.html', $form->__toString());
    }

    private function createRenderer(): FormRenderer\TemplateRenderer
    {
        $renderer = $this->templateRendererFactory->create();
        $renderer->importTemplate(FormRenderer\TemplateRenderer::DEFAULT_FORM_BLOCKS_TEMPLATE_FILE);

        return $renderer;
    }

}


(new TemplateRendererTest())->run();
