<?php
/**
 * This file is part of the nepada/form-renderer.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace NepadaTests;

use Mockery;
use Tester;


class TestCase extends Tester\TestCase
{

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

}
