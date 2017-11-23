<?php

namespace Activerules\JsonReference\Test\Loader;

use Activerules\JsonReference\Loader\CurlWebLoader;
use Activerules\JsonReference\SchemaLoadingException;

class CurlWebLoaderTest extends \PHPUnit_Framework_TestCase
{
    function test_load_loads_schemas()
    {
        $loader = new CurlWebLoader('http://');
        $response = $loader->load('localhost:1234/integer.json');
        $this->assertSame('{"type":"integer"}', json_encode($response));
    }

    function test_load_can_use_custom_options()
    {
        $this->setExpectedException(SchemaLoadingException::class);
        $loader = new CurlWebLoader('http://', [CURLOPT_NOBODY => true]);
        $loader->load('localhost:1234/integer.json');
    }

    function test_load_throws_when_schema_is_not_found()
    {
        $this->setExpectedException(SchemaLoadingException::class);
        $loader = new CurlWebLoader('http://');
        $loader->load('localhost:1234/unknown');
    }
}
