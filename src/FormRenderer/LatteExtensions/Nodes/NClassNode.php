<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer\LatteExtensions\Nodes;

use Latte\CompileException;
use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;
use Nepada\FormRenderer\Helpers;

/**
 * Improved version of `n:class="..."` that supports array arguments, e.g. `n:class="firstClass, $condition ? [foo => true, bar => true], anotherClass"`.
 * This is especially useful in combination with instances of Nette\Utils\Html, so you can do stuff like `n:class="foo, $el->class"`.
 */
final class NClassNode extends StatementNode
{

    public ArrayNode $args;

    /**
     * @param Tag $tag
     * @throws CompileException
     */
    public static function create(Tag $tag): self
    {
        if ($tag->htmlElement !== null && $tag->htmlElement->getAttribute('class') !== null) {
            throw new CompileException('It is not possible to combine class with n:class.', $tag->position);
        }

        $tag->expectArguments();
        $node = new NClassNode();
        $node->args = $tag->parser->parseArguments();
        return $node;
    }

    public function print(PrintContext $context): string
    {
        $classListCode = '$ʟ_tmp = array_filter(array_merge(...array_map([\'' . Helpers::class . '\', \'parseClassList\'], %node)))';
        $code = 'if (' . $classListCode . ') echo \' class="\', LR\Filters::escapeHtmlAttr(implode(\' \', array_unique($ʟ_tmp))), \'"\' %line;';

        return $context->format($code, $this->args, $this->position);
    }

    public function &getIterator(): \Generator
    {
        yield $this->args;
    }

}
