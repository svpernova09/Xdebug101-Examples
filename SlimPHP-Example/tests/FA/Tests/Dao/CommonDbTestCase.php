<?php

namespace FA\Tests\Dao;

use FA\Tests\FATestCase;

class CommonDbTestCase extends FATestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setUpDbInMemory();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}
