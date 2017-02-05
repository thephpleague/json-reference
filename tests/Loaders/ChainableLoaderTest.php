<?php

namespace League\JsonReference\Test\Loaders;

use League\JsonReference\Loaders\ArrayLoader;
use League\JsonReference\Loaders\ChainableLoader;

class ChainableLoaderTest extends \PHPUnit_Framework_TestCase
{
    function test_load_uses_chained_loaders()
    {
        $first  = ['first' => json_decode('{"first": "loader"}')];
        $second = ['second' => json_decode('{"second": "loader"}')];
        $loader = new ChainableLoader(new ArrayLoader($first), new ArrayLoader($second));

        $this->assertEquals($first['first'], $loader->load('first'));
        $this->assertEquals($second['second'], $loader->load('second'));
    }
}
