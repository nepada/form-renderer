<?php
/**
 * This file is part of the nepada/form-renderer.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace Nepada\FormRenderer;

use Latte;


class FormRendererMacros extends Latte\Macros\MacroSet
{

    /**
     * @param Latte\Compiler $compiler
     */
    public static function install(Latte\Compiler $compiler): void
    {
        $me = new static($compiler);
        $me->addMacro('class', null, null, [$me, 'macroClass']);
        $me->addMacro('name', [$me, 'macroName'], [$me, 'macroNameEnd'], [$me, 'macroNameAttr']);
    }

    /**
     * Improved version of `n:class="..."` that supports array arguments, e.g. `n:class="firstClass, $condition ? [foo => true, bar => true], anotherClass"`.
     * This is especially useful in combination with instances of Nette\Utils\Html, so you can do stuff like `n:class="foo, $el->class"`.
     *
     * @param Latte\MacroNode $node
     * @param Latte\PhpWriter $writer
     * @return string
     * @throws Latte\CompileException
     */
    public function macroClass(Latte\MacroNode $node, Latte\PhpWriter $writer): string
    {
        if (isset($node->htmlNode->attrs['class'])) {
            throw new Latte\CompileException('It is not possible to combine class with n:class.');
        }

        $classListCode = '$_tmp = array_filter(array_merge(...array_map([\'' . Helpers::class . '\', \'parseClassList\'], %node.array)))';
        $code = 'if (' . $classListCode . ') echo \' class="\', %escape(implode(\' \', array_unique($_tmp))), \'"\';';

        return $writer->write($code);
    }

    /********** Workaround for nette/forms#159, code below taken from FormMacros that are part of nette/forms **********/

    /**
     * <form n:name>, <input n:name>, <select n:name>, <textarea n:name>, <label n:name> and <button n:name>
     *
     * @param Latte\MacroNode $node
     * @param Latte\PhpWriter $writer
     * @return string
     * @throws Latte\CompileException
     */
    public function macroNameAttr(Latte\MacroNode $node, Latte\PhpWriter $writer): string
    {
        $words = $node->tokenizer->fetchWords();
        if (!$words) {
            throw new Latte\CompileException('Missing name in ' . $node->getNotation());
        }
        $name = array_shift($words);
        $tagName = strtolower($node->htmlNode->name);
        $node->empty = $tagName === 'input';

        $definedHtmlAttributes = array_keys($node->htmlNode->attrs);
        if (isset($node->htmlNode->macroAttrs['class'])) {
            $definedHtmlAttributes[] = 'class';
        }

        if ($tagName === 'form') {
            $node->openingCode = $writer->write(
                '<?php $form = $_form = $this->global->formsStack[] = '
                . ($name[0] === '$' ? 'is_object(%0.word) ? %0.word : ' : '')
                . '$this->global->uiControl[%0.word]; ?>',
                $name
            );
            return $writer->write(
                'echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), %0.var, false)',
                array_fill_keys($definedHtmlAttributes, null)
            );
        } else {
            $method = $tagName === 'label' ? 'getLabel' : 'getControl';
            return $writer->write(
                '$_input = ' . ($name[0] === '$' ? 'is_object(%0.word) ? %0.word : ' : '')
                . 'end($this->global->formsStack)[%0.word]; echo $_input->%1.raw'
                . ($definedHtmlAttributes ? '->addAttributes(%2.var)' : '') . '->attributes()',
                $name,
                $method . 'Part(' . implode(', ', array_map([$writer, 'formatWord'], $words)) . ')',
                array_fill_keys($definedHtmlAttributes, null)
            );
        }
    }

    /**
     * @param Latte\MacroNode $node
     * @param Latte\PhpWriter $writer
     * @throws Latte\CompileException
     */
    public function macroName(Latte\MacroNode $node, Latte\PhpWriter $writer): void
    {
        if (!$node->prefix) {
            throw new Latte\CompileException("Unknown macro {{$node->name}}, use n:{$node->name} attribute.");
        } elseif ($node->prefix !== Latte\MacroNode::PREFIX_NONE) {
            throw new Latte\CompileException("Unknown attribute n:{$node->prefix}-{$node->name}, use n:{$node->name} attribute.");
        }
    }

    /**
     * @param Latte\MacroNode $node
     * @param Latte\PhpWriter $writer
     */
    public function macroNameEnd(Latte\MacroNode $node, Latte\PhpWriter $writer): void
    {
        $tagName = strtolower($node->htmlNode->name);
        if ($tagName === 'form') {
            $node->innerContent .= '<?php echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack), false); ?>';
        } elseif ($tagName === 'label') {
            if ($node->htmlNode->empty) {
                $node->innerContent = '<?php echo $_input->getLabelPart()->getHtml() ?>';
            }
        } elseif ($tagName === 'button') {
            if ($node->htmlNode->empty) {
                $node->innerContent = '<?php echo htmlspecialchars($_input->getCaption()) ?>';
            }
        } else { // select, textarea
            $node->innerContent = '<?php echo $_input->getControl()->getHtml() ?>';
        }
    }

}
