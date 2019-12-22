<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

use Latte;
use Nette;

class TemplateRenderer implements Nette\Forms\IFormRenderer
{

    use Nette\SmartObject;

    public const DEFAULT_FORM_BLOCKS_TEMPLATE_FILE = __DIR__ . '/templates/default.latte';
    private const TEMPLATE_FILE = __DIR__ . '/templates/form.latte';

    private Nette\Application\UI\ITemplateFactory $templateFactory;

    /** @var string[] */
    private array $templateImports = [];

    private ?Nette\Bridges\ApplicationLatte\Template $template = null;

    public function __construct(Nette\Application\UI\ITemplateFactory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
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
        $latte->addFilter('safeTranslate', function (Latte\Runtime\FilterInfo $fi, ...$args) use ($form) {
            return $this->translate($form->getTranslator(), ...$args);
        });

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
        $latte->onCompile[] = function (Latte\Engine $latte): void {
            FormRendererMacros::install($latte->getCompiler());
        };

        return $template;
    }

    /**
     * @param Nette\Localization\ITranslator|null $translator
     * @param mixed ...$args
     * @return mixed
     */
    private function translate(?Nette\Localization\ITranslator $translator, ...$args)
    {
        if ($translator === null) {
            return count($args) > 0 ? reset($args) : null;
        }

        if (count($args) === 1 && $args[0] instanceof Nette\Utils\IHtmlString) {
            return $args[0];
        }

        return $translator->translate(...$args);
    }

}
