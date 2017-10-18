# Instante Extended form macros

Instante Extended form macros package provides advanced rendering features
 of Nette Forms. You have to use an extended form renderer implementing
 <code>Instante\ExtendedFormMacros\IExtendedFormRenderer</code> interface,
 for example from [Bootstrap 3 renderer](https://github.com/instante/bootstrap3renderer)
 package.

## Setup

If you want to use advanced rendering features, you have to register the Extended form macros extension in config.neon:

```
extensions:
  extendedFormMacros: Instante\ExtendedFormMacros\DI\ExtendedFormMacrosExtension
```

Or (for example when using Nette forms and Latte with another framework),
 register macros and rendering dispatcher to Latte manually: 

```php
/** @var Latte\Engine $engine */
Instante\Bootstrap3Renderer\Latte\FormMacros::install($engine->getCompiler());
$engine->addProvider('formRenderingDispatcher', new FormRenderingDispatcher);
```

## Usage

### Basic rendering

Entire form

```smarty
{control formName} or {form formName /}
```

Beginning of the form

```smarty
{form $form} or {form formName} or {$form->getRenderer()->renderBegin($form[, array $attrs])}
```

Errors

> Renders only errors that have no associated form element.
```smarty
{form.errors} or {$form->getRenderer()->renderGlobalErrors()}
```

> Renders all form errors, including errors from controls.
```smarty
{form.errors all} or {$form->getRenderer()->renderGlobalErrors(FALSE)}
```

> Renders control's errors.
```smarty
{input.errors control-name} or {input.errors $control} or {$form->getRenderer()->renderControlErrors($control)}
```

Body

> Renders all controls and groups that are not rendered yet.
```smarty
{form.body} or {$form->getRenderer()->renderBody()} 
```

End

> Renders all hidden inputs and a closing tag of the form.
```smarty
{/form} or {$form->getRenderer()->renderEnd()}
```

Container

> Renders all inputs in a container that are not rendered yet.
```smarty
{container container-name} or {$form->getRenderer()->renderContainer($form['container-name'])}
```

Group

> Renders fieldset, legend and all controls in a group that are not rendered yet.
```smarty
{group "Group name"} or {$form->getRenderer()->renderGroup($form->getGroup('Group name'))}
```

### Rendering of form components

Control-label pair

> Renders the control alongside with its label, errors and optional description.
```smarty
{pair control-name} or {$form->getRenderer()->renderPair($form['control-name'])} 
```

> Pair rendering supports smart passing of attributes to label and control element.
```smarty
{pair control-name class=>'pairClass', label-class=>'labelClass', input-class=>'inputClass'} 
```
Outputs something like (depends on IExtendedFormRenderer implementation):
```html
<div class="pairClass">
  <label class="labelClass" for="frm-foo">Some label</label>
  <input type="text" id="frm-foo" name="foo" class="inputClass" />
</div>
```
