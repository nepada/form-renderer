<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Mockery;
use Nepada\FormRenderer;
use NepadaTests\TTemplateFactoryProvider;
use NepadaTests\TTestFormProvider;
use NepadaTests\TestCase;
use Nette;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class TemplateRendererTest extends TestCase
{

    use TTemplateFactoryProvider;
    use TTestFormProvider;

    public function testUnsupportedTemplateType(): void
    {
        $template = Mockery::mock(Nette\Application\UI\ITemplate::class);

        $templateFactory = Mockery::mock(Nette\Application\UI\ITemplateFactory::class);
        $templateFactory->shouldReceive('createTemplate')->andReturn($template);

        $renderer = new FormRenderer\TemplateRenderer($templateFactory);
        Assert::exception(
            function () use ($renderer): void {
                $renderer->getTemplate();
            },
            \LogicException::class,
            'Template factory returned unsupported template type %a%, only Nette\Bridges\ApplicationLatte\Template is supported.'
        );
    }

    public function testSimple(): void
    {
        $form = $this->createTestForm();

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . '/expected/default.html', $form->__toString());
    }

    public function testErrors(): void
    {
        $form = $this->createTestForm();
        $form->addError('Form error 1.');
        $form->addError('Form error 2.');
        foreach ($form->getControls() as $control) {
            if ($control instanceof Nette\Forms\Controls\Button) {
                continue;
            }
            $control->addError("Control {$control->getName()} error 1.");
            $control->addError("Control {$control->getName()} error 2.");
        }

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . '/expected/default-errors.html', $form->__toString());
    }

    public function testRequiredControl(): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setRequired('REQUIRED');
        }

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . '/expected/default-requiredControl.html', $form->__toString());
    }

    public function testControlDescription(): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setOption('description', "Control {$control->getName()} description.");
        }

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . '/expected/default-controlDescription.html', $form->__toString());
    }

    public function testCustomControlId(): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setOption('id', sprintf('custom-%s', $control->lookupPath()));
        }

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . '/expected/default-customControlId.html', $form->__toString());
    }

    public function testCustomControlClass(): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setOption('class', sprintf('custom-%s', $control->lookupPath()));
        }

        $renderer = $this->createRenderer();
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . '/expected/default-customControlClass.html', $form->__toString());
    }

    public function testTemplateImports(): void
    {
        $form = $this->createTestForm();

        $renderer = $this->createRenderer();
        $renderer->importTemplate(__DIR__ . '/fixtures/customPair.latte');
        $renderer->importTemplate(__DIR__ . '/fixtures/customPairType.latte');
        $renderer->importTemplate(__DIR__ . '/fixtures/customControl.latte');
        $renderer->importTemplate(__DIR__ . '/fixtures/customControlType.latte');
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . '/expected/default-imports.html', $form->__toString());
    }

    private function createRenderer(): FormRenderer\TemplateRenderer
    {
        $renderer = new FormRenderer\TemplateRenderer($this->createTemplateFactory());
        $renderer->importTemplate(FormRenderer\TemplateRenderer::DEFAULT_FORM_BLOCKS_TEMPLATE_FILE);

        return $renderer;
    }

}


(new TemplateRendererTest())->run();
