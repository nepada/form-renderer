<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;

$config = [];

if (InstalledVersions::satisfies(new VersionParser(), 'nette/forms', '>=3.1.2')) {
    // method available since nette/forms 3.1.2
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Call to function method_exists\\(\\) with \'Nette\\\\\\\\Forms\\\\\\\\Form\' and \'initialize\' will always evaluate to true~',
        'path' => '../../tests/FormRenderer/TestFormFactory.php',
        'count' => 1,
    ];
}

if (InstalledVersions::satisfies(new VersionParser(), 'nette/forms', '<3.2')) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~contains generic type Nette\\\\Forms\\\\Controls\\\\BaseControl<mixed> but class Nette\\\\Forms\\\\Controls\\\\BaseControl is not generic~',
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~contains generic type Nette\\\\Forms\\\\Control<mixed> but interface Nette\\\\Forms\\\\Control is not generic~',
    ];
}

return $config;
