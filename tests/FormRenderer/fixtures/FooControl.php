<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Nette;
use Nette\Utils\Html;

class FooControl extends Nette\Forms\Controls\BaseControl
{

    public function __construct(?string $caption = null)
    {
        parent::__construct($caption);
        $this->getLabelPrototype()->addClass('label-class1');
        $this->getLabelPrototype()->addClass('label-class2');
    }

    /**
     * Generates control's HTML element.
     *
     * @return Html|string
     */
    public function getControl()
    {
        $control = parent::getControl();

        $wrapper = Html::el('div', ['data-foo' => 'foo']);
        $wrapper->addClass('control-class1');
        $wrapper->addClass('control-class2');
        $wrapper->addHtml($control);
        $wrapper->addHtml('<span>foo</span>');

        return $wrapper;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @param string|null $caption
     * @return Html|string
     */
    public function getLabel($caption = null)
    {
        $label = parent::getLabel($caption);

        return $label;
    }

}
