<?php

namespace League\JsonReference\Test;

use League\JsonReference\LoaderInterface;
use League\JsonReference\LoaderManager;
use League\JsonReference\Loaders\ArrayLoader;

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
}
