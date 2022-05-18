<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

use Latte;
use Nepada\FormRenderer\Filters\SafeTranslateFilterFactory;
use Nepada\FormRenderer\LatteExtensions\FormRendererLatteExtension;
use Nette;

class TemplateRenderer implements Nette\Forms\FormRenderer
{

    use Nette\SmartObject;

    public const DEFAULT_FORM_BLOCKS_TEMPLATE_FILE = __DIR__ . '/templates/default.latte';
    private const TEMPLATE_FILE = __DIR__ . '/templates/form.latte';

    private Nette\Application\UI\TemplateFactory $templateFactory;

    private SafeTranslateFilterFactory $safeTranslateFilterFactory;

    /**
     * @var string[]
     */
    private array $templateImports = [];

    private ?Nette\Bridges\ApplicationLatte\Template $template = null;

    public function __construct(Nette\Application\UI\TemplateFactory $templateFactory, SafeTranslateFilterFactory $safeTranslateFilterFactory)
    {
        $this->templateFactory = $templateFactory;
        $this->safeTranslateFilterFactory = $safeTranslateFilterFactory;
    }

    public function importTemplate(string $templateFile): void
    {
        array_unshift($this->templateImports, $templateFile);
    }

    public function getTemplate(): Nette\Bridges\ApplicationLatte\Template
    {
        if ($this->template === null) {
            $this->template = $this->createTemplate();
        }

        return $this->template;
    }

    public function render(Nette\Forms\Form $form): string
    {
        $template = $this->getTemplate();
        $template->setFile(self::TEMPLATE_FILE);

        $latte = $template->getLatte();
        $latte->addProvider('formRendererImports', $this->templateImports);
        $latte->addFilter('safeTranslate', $this->safeTranslateFilterFactory->create($form->getTranslator()));

        $template->form = $form;

        return (string) $template;
    }

    private function createTemplate(): Nette\Bridges\ApplicationLatte\Template
    {
        $template = $this->templateFactory->createTemplate();

        if (! $template instanceof Nette\Bridges\ApplicationLatte\Template) {
            $actualClass = get_class($template);
            $supportedClass = Nette\Bridges\ApplicationLatte\Template::class;
            throw new \LogicException("Template factory returned unsupported template type $actualClass, only $supportedClass is supported.");
        }

        $latte = $template->getLatte();
        if (version_compare(Latte\Engine::VERSION, '3', '<')) {
            $latte->onCompile[] = function (Latte\Engine $latte): void {
                Macros\FormRendererMacros::install($latte->getCompiler());
            };
        } else {
            $latte->addExtension(new FormRendererLatteExtension());
        }

        return $template;
    }

}
