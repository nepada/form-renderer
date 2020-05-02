<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

use Nepada\FormRenderer\Filters\ValidationClassFilter;
use Nette;
use Nette\Forms\Controls;
use Nette\Forms\Form;

class Bootstrap4Renderer implements Nette\Forms\IFormRenderer
{

    use Nette\SmartObject;

    public const MODE_BASIC = 'basic';
    public const MODE_INLINE = 'inline';
    public const MODE_HORIZONTAL = 'horizontal';

    public const DEFAULT_LABEL_COLS = 3;
    public const DEFAULT_CONTROL_COLS = 9;

    private const FORM_BLOCKS_TEMPLATE_FILE = __DIR__ . '/templates/bootstrap4.latte';

    private TemplateRendererFactory $templateRendererFactory;

    private ?TemplateRenderer $templateRenderer = null;

    private bool $useCustomControls = false;

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

    public function setUseCustomControls(bool $useCustomControls = true): void
    {
        $this->useCustomControls = $useCustomControls;
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
        $template->addFilter('validationClass', new ValidationClassFilter('is-invalid', null));
        $template->useCustomControls = $this->useCustomControls;
        $template->mode = $this->mode;
        $template->gridOffsetClass = $this->mode === self::MODE_HORIZONTAL ? sprintf('offset-sm-%d', $this->labelCols) : null;
        $template->gridLabelClass = $this->mode === self::MODE_HORIZONTAL ? sprintf('col-sm-%d col-form-label', $this->labelCols) : null;
        $template->gridControlClass = $this->mode === self::MODE_HORIZONTAL ? sprintf('col-sm-%d', $this->controlCols) : null;
        $template->inlineSpacingClasses = $this->mode === self::MODE_INLINE ? 'mb-2 mr-2' : null;

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
                $controlPrototype->addClass('btn-secondary');
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
