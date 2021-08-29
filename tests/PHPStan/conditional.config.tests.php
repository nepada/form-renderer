<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Nette\Bridges\ApplicationLatte\LatteFactory;

$config = [];

if (interface_exists(LatteFactory::class)) {
    // Interface renamed in nette/application 3.1
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Method NepadaTests\\\\FormRenderer\\\\TestTemplateRendererFactory::createLatteFactory\\(\\) should return Nette\\\\Bridges\\\\ApplicationLatte\\\\LatteFactory but returns class@anonymous.*~',
        'path' => '../../tests/FormRenderer/TestTemplateRendererFactory.php',
        'count' => 1,
    ];
}

if (InstalledVersions::satisfies(new VersionParser(), 'nette/forms', '<3.0.5')) {
    // false positive, fixed in nette/forms 3.0.5
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Parameter #1 \\$value of method Nette\\\\Forms\\\\Controls\\\\MultiChoiceControl::setDisabled\\(\\) expects array<bool>\\|bool, array<int, string> given~',
        'path' => '../../tests/FormRenderer/Bootstrap4RendererTest.phpt',
        'count' => 1,
    ];
}

if (InstalledVersions::satisfies(new VersionParser(), 'nette/forms', '>=3.1.2')) {
    // method available since nette/forms 3.1.2
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Call to function method_exists\\(\\) with Nette\\\\Forms\\\\Form and \'initialize\' will always evaluate to true~',
        'path' => '../../tests/FormRenderer/TestFormFactory.php',
        'count' => 1,
    ];
}

return $config;
