<?php

namespace League\JsonReference\Test\Loader;

use League\JsonReference\Loader\FileLoader;
use League\JsonReference\SchemaLoadingException;
use function peterpostmann\uri\fileuri;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    function test_load_throws_when_the_schema_is_not_found()
    {
        $this->setExpectedException(SchemaLoadingException::class);
        $loader = new FileLoader();
        $response = $loader->load(__DIR__ . '/not-found.json');
    }
    
    function test_constructor_accepts_decoder_interface() 
    {
        $decoder  = new \League\JsonReference\Decoder\YamlDecoder;
        $loader   = new FileLoader($decoder);
        $path     = substr(fileuri('../fixtures/remotes/string.yaml', __DIR__), 7);
        $response = $loader->load($path);

        $this->assertEquals((object) ['type'=>'string'], $response);
    }
}
