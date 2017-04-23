<?php

namespace League\JsonReference\Test\Loader;

use League\JsonReference\Loader\FileLoader;
use League\JsonReference\SchemaLoadingException;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    function test_load_throws_when_the_schema_is_not_found()
    {
        $this->setExpectedException(SchemaLoadingException::class);
        $loader = new FileLoader();
        $response = $loader->load(__DIR__ . '/not-found.json');
    }
}
