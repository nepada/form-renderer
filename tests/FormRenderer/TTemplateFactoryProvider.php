<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

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
        return new class () implements Nette\Bridges\ApplicationLatte\ILatteFactory
        {

            public function create(): Latte\Engine
            {
                $latte = new Latte\Engine();
                $latte->setTempDirectory(TEMP_DIR);

                $latte->addFilter('translate', fn (Latte\Runtime\FilterInfo $fi, ...$args) => reset($args));

                return $latte;
            }

        };
    }

}
