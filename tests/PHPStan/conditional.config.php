<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Nette\Localization\Translator;

$config = [];

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
