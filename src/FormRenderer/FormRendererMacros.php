<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

use Latte;

class FormRendererMacros extends Latte\Macros\MacroSet
{

    public static function install(Latte\Compiler $compiler): void
    {
        $me = new static($compiler);
        $me->addMacro('class', null, null, [$me, 'macroClass']);
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

}
