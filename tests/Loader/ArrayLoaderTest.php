<?php

namespace League\JsonReference\Test\Loader;

use League\JsonReference\SchemaLoadingException;

class ArrayLoaderTest extends \PHPUnit_Framework_TestCase
{
    function test_load_loads_schemas()
    {
        $schemas = [
            'some/schema'   => json_decode('{"hello": "world"}'),
            'string/schema' => '{"hello": "world"}',
        ];
        $loader  = new \League\JsonReference\Loader\ArrayLoader($schemas);

        $this->assertEquals($schemas['some/schema'], $loader->load('some/schema'));
        $this->assertEquals(json_decode($schemas['string/schema']), $loader->load('string/schema'));
    }

    function test_load_throws_when_not_found()
    {
        $this->setExpectedException(SchemaLoadingException::class);
        $loader = new \League\JsonReference\Loader\ArrayLoader([]);
        $loader->load('missing/path');
    }

    function test_load_throws_when_schema_is_not_an_object_or_string()
    {
        $this->setExpectedException(SchemaLoadingException::class);
        $loader = new \League\JsonReference\Loader\ArrayLoader([
            'bad/type' => []
        ]);
        $loader->load('bad/type');
    }
}
