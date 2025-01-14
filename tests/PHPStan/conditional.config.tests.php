<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;

$config = ['parameters' => ['ignoreErrors' => []]];

if (InstalledVersions::satisfies(new VersionParser(), 'nette/forms', '>=3.1.2')) {
    // method available since nette/forms 3.1.2
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Call to function method_exists\\(\\) with \'Nette\\\\\\\\Forms\\\\\\\\Form\' and \'initialize\' will always evaluate to true~',
        'path' => __DIR__ . '/../../tests/FormRenderer/TestFormFactory.php',
        'count' => 1,
    ];
}

return $config;
