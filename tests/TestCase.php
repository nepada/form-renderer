<?php
declare(strict_types = 1);

namespace NepadaTests;

use Mockery;
use Tester;

abstract class TestCase extends Tester\TestCase
{

    public function run(): void
    {
        if ($_ENV['IS_PHPSTAN'] ?? false) {
            return;
        }

        parent::run();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

}
