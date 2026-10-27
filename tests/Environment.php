<?php
declare(strict_types = 1);

namespace NepadaTests;

use Tester;

final class Environment
{

    public const TEMP_BASE_DIR = __DIR__ . '/temp';
    private const LOCK_FILE = __DIR__ . '/.lock';
    private const GC_DIVISOR = 100;
    private const GC_DIR_THRESHOLD = 200;

    private static ?string $tempDir = null;

    /**
     * Shared lock held by every running process for its whole lifetime, so that garbage collection can tell nobody is using the temp dir.
     *
     * @var resource|null
     */
    private static $lock = null;

    private function __construct()
    {
    }

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

        self::$lock ??= self::acquireLock();

        $tempDir = self::TEMP_BASE_DIR . '/' . getmypid();
        Tester\Helpers::purge($tempDir);
        self::$tempDir = $tempDir;

        return self::$tempDir;
    }

    /**
     * @return resource
     */
    private static function acquireLock()
    {
        $lock = fopen(self::LOCK_FILE, 'c');
        if ($lock === false) {
            throw new \LogicException('Cannot open lock file ' . self::LOCK_FILE);
        }
        // The non-blocking exclusive lock succeeds only when no other process is running, so the whole base dir can be safely purged
        // without waiting for anybody.
        if (self::shouldCollectGarbage() && flock($lock, LOCK_EX | LOCK_NB)) {
            Tester\Helpers::purge(self::TEMP_BASE_DIR);
        }

        // This only ever waits for a concurrent garbage collection to finish, never for other running processes.
        flock($lock, LOCK_SH);
        @mkdir(self::TEMP_BASE_DIR); // @ directory may already exist

        return $lock;
    }

    private static function shouldCollectGarbage(): bool
    {
        if (random_int(0, self::GC_DIVISOR) === 0) {
            return true;
        }

        $dirs = glob(self::TEMP_BASE_DIR . '/*', GLOB_ONLYDIR);
        return $dirs !== false && count($dirs) > self::GC_DIR_THRESHOLD;
    }

}
