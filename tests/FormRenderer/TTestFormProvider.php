<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use NepadaTests\FormRenderer\Fixtures\FooControl;
use NepadaTests\FormRenderer\Fixtures\FooPresenter;
use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

trait TTestFormProvider
{

    protected function createTestForm(): Form
    {
        $presenter = $this->mockPresenter();
        $form = new Form($presenter, 'form');
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
        $innerContainer->addSelect('selectbox', 'Selectbox', [5 => 'five', 6 => 'six']);
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

    private function mockPresenter(): Presenter
    {
        $presenter = new FooPresenter();
        $presenter->setParent(null, 'Foo');

        $url = new Nette\Http\UrlScript('https://example.com/');
        $httpRequest = new Nette\Http\Request($url);
        $httpResponse = new Nette\Http\Response();
        $router = new Nette\Application\Routers\Route('/<presenter>/<action>');
        $presenter->injectPrimary(null, null, $router, $httpRequest, $httpResponse);

        $request = new Nette\Application\Request('Foo', 'GET');
        $requestReflection = new \ReflectionProperty(Presenter::class, 'request');
        $requestReflection->setAccessible(true);
        $requestReflection->setValue($presenter, $request);

        $initGlobalParametersReflection = new \ReflectionMethod(Presenter::class, 'initGlobalParameters');
        $initGlobalParametersReflection->setAccessible(true);
        $initGlobalParametersReflection->invoke($presenter);

        return $presenter;
    }

}
