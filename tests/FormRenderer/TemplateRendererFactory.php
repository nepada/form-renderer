<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Latte;
use Nepada\FormRenderer\ITemplateRendererFactory;
use Nepada\FormRenderer\TemplateRenderer;
use Nette;

final class TemplateRendererFactory implements ITemplateRendererFactory
{

    use Nette\SmartObject;

    public function create(): TemplateRenderer
    {
        return new TemplateRenderer($this->createTemplateFactory(), new SafeTranslateFilterFactory());
    }

    private function createTemplateFactory(): Nette\Bridges\ApplicationLatte\TemplateFactory
    {
        return new Nette\Bridges\ApplicationLatte\TemplateFactory($this->createLatteFactory());
    }

    private function createLatteFactory(): Nette\Bridges\ApplicationLatte\ILatteFactory
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
