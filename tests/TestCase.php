<?php
declare(strict_types = 1);

namespace NepadaTests;

use Mockery;
use Tester;

abstract class TestCase extends Tester\TestCase
{

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function resetHttpGlobalVariables(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_FILES = [];
        $_COOKIE['_nss'] = '1';
        $_POST = [];
        $_GET = [];
    }

}
