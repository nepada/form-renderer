Form Renderer
====================

[![Build Status](https://travis-ci.org/nepada/form-renderer.svg?branch=master)](https://travis-ci.org/nepada/form-renderer)
[![Coverage Status](https://coveralls.io/repos/github/nepada/form-renderer/badge.svg?branch=master)](https://coveralls.io/github/nepada/form-renderer?branch=master)
[![Downloads this Month](https://img.shields.io/packagist/dm/nepada/form-renderer.svg)](https://packagist.org/packages/nepada/form-renderer)
[![Latest stable](https://img.shields.io/packagist/v/nepada/form-renderer.svg)](https://packagist.org/packages/nepada/form-renderer)


Installation
------------

Via Composer:

```sh
$ composer require nepada/form-renderer
```

Register the extension in `config.neon`:

```yaml
extensions:
    formRenderer: Nepada\Bridges\FormRendererDI\FormRendererExtension
```


Usage
-----

Nette gives you two options how to render a form:
1) Render the whole form manually in Latte template using form macros. This way you have complete control over the rendering, but writing all the templates quickly gets repetitive.
2) Render it using a form renderer, e.g. `DefaultFormRenderer` from `nette/forms`. `DefaultFormRenderer` is very customizable, but it's hard to setup special rendering rules for only some controls of a form, or add support for rendering of new form control types. 

`nepada/form-renderer` is built on top of Latte templates and their powerful blocks, thus combining strengths of manual rendering with DRY principle of form renderers.

### TemplateRenderer

You can use `TemplateRendererFactory` service to create the renderer with preconfigured [default template](src/FormRenderer/templates/default.latte). It renders a form more or less the same way as `DefaultFormRenderer`.

#### Customizing rendering

You can customize rendering by importing blocks from a template file - blocks imported later override the previously imported ones of the same name. You can also pass custom variables to the template.

```php
/** @var Nepada\FormRenderer\TemplateRendererFactory $factory */
$renderer = $factory->create();
$renderer->importTemplate(__DIR__ . '/custom-form-rendering-blocks.latte');
$renderer->getTemlate()->foo = 'bar'; 
$form->setRenderer($renderer);
```

**Tips:**
- You can define special rendering for a specific control of a form in `#control-name-*` block (e.g. `#control-name-container-subcontainer-foocontrol`).
- If you need special rendering for both a control and its label, define it in `#pair-name-*` block.
- Rendering of different control types (based on the value of `$control->getOption('type')`) is controlled by blocks `#control-type-*` and `#pair-type-*`. The default template actually uses this for buttons (rendering of consecutive buttons in one row).
- You can also specify template files to be imported in your `config.neon`:
  ```yaml
  formRenderer:
      default:
          imports:
              - %appDir%/templates/@form-extras1.latte
              - %appDir%/templates/@form-extras2.latte
  ```  

For a complete overview of supported blocks and better understanding how the renderer works, please read the code of the [template](src/FormRenderer/templates/default.latte). 

#### Creating custom TemplateRenderer setup from scratch

You don't need to use the default template, you can create one from scratch with blocks tailored to your needs. You can define factory for your custom setup like this:
```yaml
services:
    customRenderer:
        implement: Nepada\FormRenderer\TemplateRendererFactory
        setup:
          - importTemplate('%appDir%/templates/@form.latte')
          - importTemplate('%appDir%/templates/@form-extras.latte')
```
Just make sure that one of your template files defines block named `#form` - this is used as a starting point for the rendering.

#### Filter `safeTranslate` in templates

For translations the templates may use custom `safeTranslate` filter. The key differences from standard `translate` filter are:
1) It avoids translating instances of `Nette\Utils\IHtmlString` and `Latte\Runtime\IHtmlString`.
2) It uses a translator from the form instance that is being rendered.
3) If the form has no translator set, than it simply returns the passed string untranslated. 

#### Improved version of `n:class` macro

In all form templates there is also available an improved version of `n:class` macro that supports merging of classes from `Nette\Utils\Html` instances. You can do stuff like `<input n:name="$control" n:class="$control->controlPrototype->class, form-control">` and don't need to worry if the class attribute is really represented as a string or array, it all just works. 


### Bootstrap3Renderer

Form renderer compatible with Bootstrap 3, it internally uses `TemplateRenderer` with [custom template](src/FormRenderer/templates/boostrap3.latte).

The template supports three rendering modes:
```php
$renderer->setBasicMode(); // Basic form
$renderer->setInlineMode(); // Inline form
$renderer->setHorizontalMode(4, 8); // Horizontal form (you can optionally set the size of label and control columns)
```

Rendering mode and template files with custom rendering rules can be set in your `config.neon`:
```yaml
formRenderer:
    bootstrap3:
        mode: horizontal
        imports:
            - %appDir%/templates/@form-extras.latte
```

`Bootstrap3Renderer` makes a couple of adjustments to the form before it is passed over to `TemplateRenderer`:
1) It adds `btn btn-primary` classes to the control prototype of first `SubmitButton` in the form, unless there already is such a control in the form.
2) It adds `btn btn-default` classes to the control prototype of every `Button` control, unless it already has `btn` class set.
3) Changes `type` option on all `CheckboxList` controls from `checkbox` to `checkboxlist`.


### Bootstrap4Renderer

Form renderer compatible with Bootstrap 4, it internally uses `TemplateRenderer` with [custom template](src/FormRenderer/templates/boostrap4.latte).

The template supports three rendering modes:
```php
$renderer->setBasicMode(); // Basic form
$renderer->setInlineMode(); // Inline form
$renderer->setHorizontalMode(4, 8); // Horizontal form (you can optionally set the size of label and control columns)
```

You can enable the use of [custom form controls](https://getbootstrap.com/docs/4.4/components/forms/#custom-forms) by `$renderer->setUseCustomControls(true)`. To render a checkbox as a switch, you need to set type option: `$checkboxInput->setOption('type', 'switch')`. 

Rendering mode, custom form controls and template files with custom rendering rules can be set in your `config.neon`:
```yaml
formRenderer:
    bootstrap4:
        mode: horizontal
        useCustomControls: true
        imports:
            - %appDir%/templates/@form-extras.latte
```

`Bootstrap4Renderer` makes a couple of adjustments to the form before it is passed over to `TemplateRenderer`:
1) It adds `btn btn-primary` classes to the control prototype of first `SubmitButton` in the form, unless there already is such a control in the form.
2) It adds `btn btn-secondary` classes to the control prototype of every `Button` control, unless it already has `btn` class set.
3) Changes `type` option on all `CheckboxList` controls from `checkbox` to `checkboxlist`.
