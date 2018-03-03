<?php
declare(strict_types = 1);

namespace NepadaTests;

use Latte;
use Nette;

trait TTemplateFactoryProvider
{

    protected function createTemplateFactory(): Nette\Bridges\ApplicationLatte\TemplateFactory
    {
        return new Nette\Bridges\ApplicationLatte\TemplateFactory($this->createLatteFactory());
    }

    protected function createLatteFactory(): Nette\Bridges\ApplicationLatte\ILatteFactory
    {
        return new class() implements Nette\Bridges\ApplicationLatte\ILatteFactory
        {

            /**
             * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
             * @return Latte\Engine
             */
            public function create()
            {
                $latte = new Latte\Engine();
                $latte->setTempDirectory(TEMP_DIR);

                $latte->addFilter('translate', function (Latte\Runtime\FilterInfo $fi, ...$args) {
                    return reset($args);
                });

                return $latte;
            }

        };
    }

}
