<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer\LatteExtensions;

use Latte;

final class FormRendererLatteExtension extends Latte\Extension
{

    /**
     * @return array<string, callable(Latte\Compiler\Tag, Latte\Compiler\TemplateParser): (Latte\Compiler\Node|\Generator|void)|\stdClass>
     */
    public function getTags(): array
    {
        return [
            'n:class' => [Nodes\NClassNode::class, 'create'],
        ];
    }

}
