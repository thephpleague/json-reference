<?php

namespace Activerules\JsonReference\Test\Loader;

use Activerules\JsonReference\Loader\FileGetContentsWebLoader;

class FileGetContentsWebLoaderTest extends \PHPUnit_Framework_TestCase
{
    function test_it_loads_schemas()
    {
        $loader = new FileGetContentsWebLoader('http://');
        $response = $loader->load('localhost:1234/integer.json');
        $this->assertSame('{"type":"integer"}', json_encode($response));
    }

    /**
     * @expectedException \Activerules\JsonReference\SchemaLoadingException
     */
    function test_it_throws_when_the_schema_is_not_found()
    {
        $loader = new FileGetContentsWebLoader('http://');
        $loader->load('localhost:1234/unknown');
    }

    /**
     * @expectedException \Activerules\JsonReference\SchemaLoadingException
     */
    function test_it_throws_when_the_response_is_empty()
    {
        $loader = new FileGetContentsWebLoader('http://');
        $loader->load('localhost:1234/empty.json');
    }
}
