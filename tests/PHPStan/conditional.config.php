<?php
declare(strict_types = 1);

use Latte\Engine;
use Nette\Localization\Translator;

$config = [];

if (version_compare(Engine::VERSION, '3.0', '<')) {
    $config['parameters']['excludePaths']['analyse'][] = __DIR__ . '/../../src/FormRenderer/LatteExtensions/FormRendererLatteExtension.php';
    $config['parameters']['excludePaths']['analyse'][] = __DIR__ . '/../../src/FormRenderer/LatteExtensions/Nodes/NClassNode.php';
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~If condition is always true\\.~',
        'path' => '../../src/FormRenderer/TemplateRenderer.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Call to an undefined method Latte\\\\Engine::addExtension\\(\\)\\.~',
        'path' => '../../src/FormRenderer/TemplateRenderer.php',
        'count' => 1,
    ];
} else {
    $config['parameters']['excludePaths']['analyse'][] = __DIR__ . '/../../src/FormRenderer/Macros/FormRendererMacros.php';
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~If condition is always false\\.~',
        'path' => '../../src/FormRenderer/TemplateRenderer.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Access to an undefined property Latte\\\\Engine::\\$onCompile\\.~',
        'path' => '../../src/FormRenderer/TemplateRenderer.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Call to an undefined method Latte\\\\Engine::getCompiler\\(\\)\\.~',
        'path' => '../../src/FormRenderer/TemplateRenderer.php',
        'count' => 1,
    ];
}

if (PHP_VERSION_ID >= 8_00_00) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Missing native return typehint mixed$~',
        'path' => '../../src/FormRenderer/Filters/SafeTranslateFilter.php',
        'count' => 1,
    ];
}

// Detecting version from composer installed versions does not work, because it gets hijacked by phpstan.phar
$isNetteUtils4 = (new ReflectionMethod(Translator::class, 'translate'))->getParameters()[0]->getType() instanceof ReflectionUnionType;
if ($isNetteUtils4) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Parameter \\#1 \\$message of method Nette\\\\Localization\\\\Translator\\:\\:translate\\(\\) expects string\\|Stringable, mixed given\\.$~',
        'path' => '../../src/FormRenderer/Filters/SafeTranslateFilter.php',
        'count' => 1,
    ];
}

return $config;
