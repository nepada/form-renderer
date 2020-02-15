<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Nepada\FormRenderer;
use NepadaTests\TestCase;
use Nette;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class Bootstrap3RendererTest extends TestCase
{

    private TemplateRendererFactory $templateRendererFactory;

    private TestFormFactory $testFormFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateRendererFactory = new TemplateRendererFactory();
        $this->testFormFactory = new TestFormFactory();
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testSimple(string $mode): void
    {
        $form = $this->createTestForm();
        $renderer = $this->createRenderer($mode);
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . "/expected/bootstrap3-{$mode}.html", $form->__toString());
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testErrors(string $mode): void
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

        $renderer = $this->createRenderer($mode);
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . "/expected/bootstrap3-{$mode}-errors.html", $form->__toString());
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testRequiredControl(string $mode): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setRequired('REQUIRED');
        }

        $renderer = $this->createRenderer($mode);
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . "/expected/bootstrap3-{$mode}-requiredControl.html", $form->__toString());
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testDefaultTemplateControlDescription(string $mode): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setOption('description', "Control {$control->getName()} description.");
        }

        $renderer = $this->createRenderer($mode);
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . "/expected/bootstrap3-{$mode}-controlDescription.html", $form->__toString());
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testDefaultTemplateCustomControlId(string $mode): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setOption('id', sprintf('custom-%s', $control->lookupPath()));
        }

        $renderer = $this->createRenderer($mode);
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . "/expected/bootstrap3-{$mode}-customControlId.html", $form->__toString());
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testDefaultTemplateCustomControlClass(string $mode): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setOption('class', sprintf('custom-%s', $control->lookupPath()));
        }

        $renderer = $this->createRenderer($mode);
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . "/expected/bootstrap3-{$mode}-customControlClass.html", $form->__toString());
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testTemplateImports(string $mode): void
    {
        $form = $this->createTestForm();

        $renderer = $this->createRenderer($mode);
        $renderer->importTemplate(__DIR__ . '/Fixtures/customPair.latte');
        $renderer->importTemplate(__DIR__ . '/Fixtures/customPairType.latte');
        $renderer->importTemplate(__DIR__ . '/Fixtures/customControl.latte');
        $renderer->importTemplate(__DIR__ . '/Fixtures/customControlType.latte');
        $form->setRenderer($renderer);

        Assert::matchFile(__DIR__ . "/expected/bootstrap3-{$mode}-imports.html", $form->__toString());
    }

    /**
     * @return mixed[]
     */
    public function getRendererModes(): array
    {
        return [
            ['mode' => FormRenderer\Bootstrap3Renderer::MODE_BASIC],
            ['mode' => FormRenderer\Bootstrap3Renderer::MODE_INLINE],
            ['mode' => FormRenderer\Bootstrap3Renderer::MODE_HORIZONTAL],
        ];
    }

    protected function createTestForm(): Nette\Application\UI\Form
    {
        $form = $this->testFormFactory->create();

        $warningButton = $form->addButton('warning');
        $warningButton->getControlPrototype()->addClass('btn btn-warning');

        $checkboxList = $form->addCheckboxList('inlinecheckboxlist', 'InlineCheckboxList', ['foo', 'bar']);
        $checkboxList->getSeparatorPrototype()->setName('');

        $radioList = $form->addRadioList('inlineradiolist', 'InlineRadioList', ['foo', 'bar']);
        $radioList->getSeparatorPrototype()->setName('');

        return $form;
    }

    private function createRenderer(string $mode): FormRenderer\Bootstrap3Renderer
    {
        $renderer = new FormRenderer\Bootstrap3Renderer($this->templateRendererFactory);
        if ($mode === FormRenderer\Bootstrap3Renderer::MODE_INLINE) {
            $renderer->setInlineMode();
        } elseif ($mode === FormRenderer\Bootstrap3Renderer::MODE_HORIZONTAL) {
            $renderer->setHorizontalMode();
        }

        return $renderer;
    }

}


(new Bootstrap3RendererTest())->run();
