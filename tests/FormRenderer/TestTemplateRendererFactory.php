<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer;

use Latte;
use Nepada\FormRenderer\TemplateRenderer;
use Nepada\FormRenderer\TemplateRendererFactory;
use NepadaTests\Environment;
use Nette;

final class TestTemplateRendererFactory implements TemplateRendererFactory
{

    use Nette\SmartObject;

    public function create(): TemplateRenderer
    {
        return new TemplateRenderer($this->createTemplateFactory(), new TestSafeTranslateFilterFactory());
    }

    private function createTemplateFactory(): Nette\Bridges\ApplicationLatte\TemplateFactory
    {
        return new Nette\Bridges\ApplicationLatte\TemplateFactory($this->createLatteFactory());
    }

    private function createLatteFactory(): Nette\Bridges\ApplicationLatte\LatteFactory
    {
        return new class () implements Nette\Bridges\ApplicationLatte\LatteFactory
        {

            public function create(): Latte\Engine
            {
                $latte = new Latte\Engine();
                $latte->setTempDirectory(Environment::getTempDir());

                $latte->addFilter('translate', fn (Latte\Runtime\FilterInfo $fi, ...$args) => reset($args));

                return $latte;
            }

        };
    }

}
