<?php
declare(strict_types = 1);

namespace NepadaTests;

use Nette;
use Tester;

final class Environment
{

    use Nette\StaticClass;

    public const TEMP_BASE_DIR = __DIR__ . '/temp';
    private const LOCK_FILE = __DIR__ . '/.lock';
    private const GC_DIVISOR = 100;

    private static ?string $tempDir = null;

    /**
     * @var resource
     */
    private static $lock;

    public static function setup(): void
    {
        Tester\Environment::setup();
        date_default_timezone_set('Europe/Prague');
        ini_set('session.save_path', self::getTempDir());
    }

    public static function getTempDir(): string
    {
        if (self::$tempDir !== null) {
            return self::$tempDir;
        }

        self::prepareTempBaseDir();

        $tempDir = self::TEMP_BASE_DIR . '/' . getmypid();
        Tester\Helpers::purge($tempDir);
        self::$tempDir = $tempDir;

        return self::$tempDir;
    }

    private static function prepareTempBaseDir(): void
    {
        $lock = fopen(self::LOCK_FILE, 'w');
        assert(is_resource($lock));
        self::$lock = $lock;

        if (self::shouldCollectGarbage() && flock(self::$lock, LOCK_EX)) {
            Tester\Helpers::purge(self::TEMP_BASE_DIR);

        } else {
            flock(self::$lock, LOCK_SH);
            @mkdir(self::TEMP_BASE_DIR);
        }
    }

    private static function shouldCollectGarbage(): bool
    {
        return rand(0, self::GC_DIVISOR) === 0;
    }

}
