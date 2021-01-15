<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Nepada\FormRenderer;
use NepadaTests\TestCase;
use Nette;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class Bootstrap4RendererTest extends TestCase
{

    private TestTemplateRendererFactory $templateRendererFactory;

    private TestFormFactory $testFormFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateRendererFactory = new TestTemplateRendererFactory();
        $this->testFormFactory = new TestFormFactory();

        $this->resetHttpGlobalVariables();
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

        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}.html", $form->__toString());
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testRenderValidState(string $mode): void
    {
        $renderer = $this->createRenderer($mode);
        $renderer->setRenderValidState(true);

        $form = $this->createTestForm();
        $form->setRenderer($renderer);
        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}.html", $form->__toString(), 'enabled renderValidState, but not submitted form');

        $renderer->setRenderValidState(false);
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $form = $this->createTestForm();
        $form->setRenderer($renderer);
        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}.html", $form->__toString(), 'submitted form, but disabled renderValidState');

        $renderer->setRenderValidState(true);
        $form->setValues([
            'text' => 'foo',
            'container' => [
                'checkbox' => true,
                'checkboxlist' => 1,
                'radiolist' => 3,
                'innerContainer' => ['selectbox' => 5],
            ],
            'inlinecheckboxlist' => '1',
            'inlineradiolist' => '1',
            'range' => 10,
            'switch' => true,
        ]);
        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}-renderValidState.html", $form->__toString(), 'render valid state');
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testUseCustomControls(string $mode): void
    {
        $form = $this->createTestForm();
        $renderer = $this->createRenderer($mode);
        $renderer->setUseCustomControls(true);
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}-customControls.html", $form->__toString());
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

        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}-errors.html", $form->__toString());

        if ($mode !== FormRenderer\Bootstrap4Renderer::MODE_INLINE) {
            $renderer->setUseErrorTooltips();
            HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}-errors-tooltips.html", $form->__toString());
        }
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

        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}-requiredControl.html", $form->__toString());
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testControlDescription(string $mode): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setOption('description', "Control {$control->getName()} description.");
        }

        $renderer = $this->createRenderer($mode);
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}-controlDescription.html", $form->__toString());
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testCustomControlId(string $mode): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setOption('id', sprintf('custom-%s', $control->lookupPath()));
        }

        $renderer = $this->createRenderer($mode);
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}-customControlId.html", $form->__toString());
    }

    /**
     * @dataProvider getRendererModes
     * @param string $mode
     */
    public function testCustomControlClass(string $mode): void
    {
        $form = $this->createTestForm();
        foreach ($form->getControls() as $control) {
            $control->setOption('class', sprintf('custom-%s', $control->lookupPath()));
        }

        $renderer = $this->createRenderer($mode);
        $form->setRenderer($renderer);

        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}-customControlClass.html", $form->__toString());
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

        HtmlAssert::matchFile(__DIR__ . "/expected/bootstrap4-{$mode}-imports.html", $form->__toString());
    }

    /**
     * @return mixed[]
     */
    public function getRendererModes(): array
    {
        return [
            ['mode' => FormRenderer\Bootstrap4Renderer::MODE_BASIC],
            ['mode' => FormRenderer\Bootstrap4Renderer::MODE_INLINE],
            ['mode' => FormRenderer\Bootstrap4Renderer::MODE_HORIZONTAL],
        ];
    }

    protected function createTestForm(): Nette\Forms\Form
    {
        $form = $this->testFormFactory->create();

        $form->getComponent('textarea')->setValue('Lorem ipsum');

        $warningButton = $form->addButton('warning');
        $warningButton->getControlPrototype()->addClass('btn btn-warning');

        $checkboxList = $form->addCheckboxList('inlinecheckboxlist', 'InlineCheckboxList', ['foo', 'bar']);
        $checkboxList->setDisabled(['0']);
        $checkboxList->getSeparatorPrototype()->setName('');

        $radioList = $form->addRadioList('inlineradiolist', 'InlineRadioList', ['foo', 'bar']);
        $radioList->setDisabled(['0']);
        $radioList->getSeparatorPrototype()->setName('');

        $range = $form->addText('range', 'Range');
        $range->setHtmlType('range')->setOption('type', 'range');

        $switch = $form->addCheckbox('switch', 'Switch');
        $switch->setOption('type', 'switch');
        $switch->setOption('description', 'Switch description');

        return $form;
    }

    private function createRenderer(string $mode): FormRenderer\Bootstrap4Renderer
    {
        $renderer = new FormRenderer\Bootstrap4Renderer($this->templateRendererFactory);
        if ($mode === FormRenderer\Bootstrap4Renderer::MODE_INLINE) {
            $renderer->setInlineMode();
        } elseif ($mode === FormRenderer\Bootstrap4Renderer::MODE_HORIZONTAL) {
            $renderer->setHorizontalMode();
        }

        return $renderer;
    }

}


(new Bootstrap4RendererTest())->run();
