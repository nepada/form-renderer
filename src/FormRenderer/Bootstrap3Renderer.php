<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

use Nette;
use Nette\Forms\Controls;
use Nette\Forms\Form;

class Bootstrap3Renderer implements Nette\Forms\IFormRenderer
{

    use Nette\SmartObject;

    public const MODE_BASIC = 'basic';
    public const MODE_INLINE = 'inline';
    public const MODE_HORIZONTAL = 'horizontal';

    public const DEFAULT_LABEL_COLS = 3;
    public const DEFAULT_CONTROL_COLS = 9;

    private const FORM_BLOCKS_TEMPLATE_FILE = __DIR__ . '/templates/bootstrap3.latte';

    private TemplateRendererFactory $templateRendererFactory;

    private ?TemplateRenderer $templateRenderer = null;

    private string $mode = self::MODE_BASIC;

    private int $labelCols = self::DEFAULT_LABEL_COLS;

    private int $controlCols = self::DEFAULT_CONTROL_COLS;

    public function __construct(TemplateRendererFactory $templateRendererFactory)
    {
        $this->templateRendererFactory = $templateRendererFactory;
    }

    public function importTemplate(string $templateFile): void
    {
        $this->getTemplateRenderer()->importTemplate($templateFile);
    }

    public function setBasicMode(): void
    {
        $this->mode = self::MODE_BASIC;
    }

    public function setInlineMode(): void
    {
        $this->mode = self::MODE_INLINE;
    }

    public function setHorizontalMode(int $labelCols = self::DEFAULT_LABEL_COLS, int $controlCols = self::DEFAULT_CONTROL_COLS): void
    {
        $this->mode = self::MODE_HORIZONTAL;
        $this->labelCols = $labelCols;
        $this->controlCols = $controlCols;
    }

    public function render(Form $form): string
    {
        $this->prepareForm($form);

        $templateRenderer = $this->getTemplateRenderer();
        $template = $templateRenderer->getTemplate();
        $template->mode = $this->mode;
        $template->gridOffsetClass = sprintf('col-sm-offset-%d', $this->labelCols);
        $template->gridLabelClass = sprintf('col-sm-%d', $this->labelCols);
        $template->gridControlClass = sprintf('col-sm-%d', $this->controlCols);

        return $templateRenderer->render($form);
    }

    protected function getTemplateRenderer(): TemplateRenderer
    {
        if ($this->templateRenderer === null) {
            $this->templateRenderer = $this->templateRendererFactory->create();
            $this->templateRenderer->importTemplate(self::FORM_BLOCKS_TEMPLATE_FILE);
        }

        return $this->templateRenderer;
    }

    protected function prepareForm(Form $form): void
    {
        $primaryButton = $this->findPrimaryButton($form);
        /** @var Controls\Button $control */
        foreach ($form->getComponents(true, Controls\Button::class) as $control) {
            $controlPrototype = $control->getControlPrototype();
            $classes = Helpers::parseClassList($controlPrototype->getClass());
            if (in_array('btn', $classes, true)) {
                continue;
            }
            $controlPrototype->addClass('btn');
            if ($control instanceof Controls\SubmitButton && $primaryButton === null) {
                $controlPrototype->addClass('btn-primary');
                $primaryButton = $control;
            } else {
                $controlPrototype->addClass('btn-default');
            }
        }

        /** @var Controls\CheckboxList $control */
        foreach ($form->getComponents(true, Controls\CheckboxList::class) as $control) {
            if ($control->getOption('type') === 'checkbox') {
                $control->setOption('type', 'checkboxlist');
            }
        }
    }

    protected function findPrimaryButton(Form $form): ?Controls\SubmitButton
    {
        /** @var Controls\SubmitButton $control */
        foreach ($form->getComponents(true, Controls\SubmitButton::class) as $control) {
            $classes = Helpers::parseClassList($control->getControlPrototype()->getClass());
            if (in_array('btn-primary', $classes, true)) {
                return $control;
            }
        }

        return null;
    }

}
