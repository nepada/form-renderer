<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;

$config = [];

if (InstalledVersions::satisfies(new VersionParser(), 'nette/forms', '>=3.1.2')) {
    // method available since nette/forms 3.1.2
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Call to function method_exists\\(\\) with Nette\\\\Forms\\\\Form and \'initialize\' will always evaluate to true~',
        'path' => '../../tests/FormRenderer/TestFormFactory.php',
        'count' => 1,
    ];
}

if (PHP_VERSION_ID >= 8_00_00) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Missing native return typehint mixed$~',
        'path' => '../../tests/FormRenderer/Filters/Fixtures/CustomControl.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Missing native return typehint \\\\Nette\\\\Utils\\\\Html\\|string$~',
        'path' => '../../tests/FormRenderer/Fixtures/FooControl.php',
        'count' => 1,
    ];
}

return $config;
