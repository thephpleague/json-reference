<?php

namespace League\JsonReference\Test;

use League\JsonReference\Loader\ArrayLoader;
use League\JsonReference\LoaderInterface;
use League\JsonReference\LoaderManager;
use League\JsonReference\DecoderManager;

class LoaderManagerTest extends \PHPUnit_Framework_TestCase
{
    function test_can_get_all_loaders_indexed_by_prefix()
    {
        $manager = new LoaderManager();
        $loaders = $manager->getLoaders();
        $this->assertArrayHasKey('file', $loaders);
        $this->assertInstanceOf(LoaderInterface::class, $loaders['file']);
        $this->assertArrayHasKey('http', $loaders);
        $this->assertInstanceOf(LoaderInterface::class, $loaders['http']);
        $this->assertArrayHasKey('https', $loaders);
        $this->assertInstanceOf(LoaderInterface::class, $loaders['https']);
    }

    function test_getLoader_throws_when_the_loader_does_not_exist()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $manager = new LoaderManager();
        $manager->getLoader('couchdb');
    }

    function test_can_register_loader()
    {
        $loader  = new ArrayLoader([]);
        $manager = new LoaderManager();
        $manager->registerLoader('http', $loader);
        $this->assertSame($loader, $manager->getLoader('http'));
    }

    function test_it_doesnt_use_defaults_if_loaders_are_provided()
    {
        $loaders  = [
            'array' => new ArrayLoader([])
        ];

        $manager = new LoaderManager($loaders);

        $this->assertFalse($manager->hasLoader('file'));
        $this->assertFalse($manager->hasLoader('http'));
        $this->assertFalse($manager->hasLoader('https'));
        $this->assertTrue($manager->hasLoader('array'));
    }
    
    function test_it_returns_decoder_manager()
    {
        $loaderManager  = new LoaderManager();
        $this->assertInstanceOf(DecoderManager::class, $loaderManager->getDecoderManager());       
    }

    function test_it_loads_from_suffix_first()
    {
        $loaderManager = new LoaderManager();
        $result        = $loaderManager->getLoader('http')->load('localhost:1234/schema.php?type=application/xml+json&content={"type":"string"}');

        $this->assertEquals((object) ['type' => 'string'], $result);  
    }

    function test_it_loads_from_type_second()
    {
        $loaderManager = new LoaderManager();
        $result        = $loaderManager->getLoader('http')->load('localhost:1234/schema.php?type=application/json&content={"type":"string"}');

        $this->assertEquals((object) ['type' => 'string'], $result);  
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    function test_it_loads_from_extension_third_and_fail()
    {
        $loaderManager = new LoaderManager();
        $loaderManager->getDecoderManager()->setDefaultType(null);
        $result        = $loaderManager->getLoader('http')->load('localhost:1234/schema.php?type=&content={"type":"string"}');
    }

    function test_it_loads_from_extension_third_and_succeed()
    {
        $loaderManager = new LoaderManager();
        $loaderManager->getDecoderManager()->setDefaultType(null);
        $jsonDecoder   = $loaderManager->getDecoderManager()->getDecoder('json');
        $loaderManager->getDecoderManager()->registerDecoder('php', $jsonDecoder);
        $result        = $loaderManager->getLoader('http')->load('localhost:1234/schema.php?type=&content={"type":"string"}');

        $this->assertEquals((object) ['type' => 'string'], $result);  
    }
}
