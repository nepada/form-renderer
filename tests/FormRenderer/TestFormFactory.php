<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use NepadaTests\FormRenderer\Fixtures\FooControl;
use Nette;
use Nette\Forms\Form;

final class TestFormFactory
{

    use Nette\SmartObject;

    public function create(): Form
    {
        $form = new Form();
        if (method_exists($form, 'initialize')) { // BC with nette/forms <3.1.2
            $form::initialize(true);
        }
        $form->setAction('#');
        $form->getElementPrototype()->addClass('form-class1');
        $form->getElementPrototype()->addClass('form-class2');

        $form->addHidden('hidden');

        $group = $form->addGroup('Group 1');
        $group->setOption('description', 'Group 1 description.');
        $group->setOption('embedNext', true);
        $group->setOption('id', 'custom-group-id');
        $form->addText('text', 'Text');
        $form->addTextArea('textarea', 'TextArea');

        $group = $form->addGroup('Group 2');
        $group->setOption('label', Nette\Utils\Html::el('span', 'Group 2 label'));
        $group->setOption('description', Nette\Utils\Html::el('span', 'Group 2 description.'));
        $group->setOption('embedNext', true);
        $container = $form->addContainer('container');
        $container->addCheckbox('checkbox', 'Checkbox');
        $container->addCheckboxList('checkboxlist', 'CheckBoxList', [1 => 'one', 2 => 'two']);
        $container->addRadioList('radiolist', 'RadioList', [3 => 'three', 4 => 'four']);

        $innerContainer = $container->addContainer('innerContainer');
        $innerContainer->addSelect('selectbox', 'Selectbox', [5 => 'five', 6 => 'six'])
            ->setPrompt('');
        $uploadInput = $innerContainer->addUpload('upload', 'Upload');
        if (iterator_count($uploadInput->getRules()->getIterator()) === 1) { // compatibility with nette/forms <3.0.3
            $uploadInput->addRule(Form::MAX_FILE_SIZE, null, 2 ** 20);
        }

        $form->setCurrentGroup(null);
        $form->addComponent(new FooControl('Foo'), 'foo');
        $form->addSubmit('send', 'SubmitButton');
        $form->addButton('button', 'Button');

        return $form;
    }

}
