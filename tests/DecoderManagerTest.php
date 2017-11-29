<?php

namespace League\JsonReference\Test;

use League\JsonReference\Decoder\JsonDecoder;
use League\JsonReference\Decoder\YamlDecoder;
use League\JsonReference\DecoderInterface;
use League\JsonReference\DecoderManager;

class DecoderManagerTest extends \PHPUnit_Framework_TestCase
{
    function default_decoder()
    {
        $manager = new DecoderManager();

        return [
            ['json', $manager],
            ['text/json', $manager],
            ['application/json', $manager],
            ['+json', $manager],
        ];        
    }

    function json_decoder()
    {
        $manager = new DecoderManager();
        $manager->registerJsonDecoder();

        return [
            ['json', $manager],
            ['text/json', $manager],
            ['application/json', $manager],
            ['+json', $manager],
        ];
    }

    function yaml_decoder()
    {
        $manager = new DecoderManager();
        $manager->registerYamlDecoder();
        
        return [
            ['yml', $manager],
            ['yaml', $manager],
            ['text/yaml', $manager],
            ['application/x-yaml', $manager],
            ['+yaml', $manager],
        ];
    }

    /**
     * @dataProvider default_decoder
     * @dataProvider json_decoder
     * @dataProvider yaml_decoder
     */
    function test_decoder_presence($key, $manager)
    {
        $decoders = $manager->getDecoders();
        $this->assertArrayHasKey($key, $decoders);
        $this->assertInstanceOf(DecoderInterface::class, $decoders[$key]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function test_getDecoder_throws_when_the_decoder_does_not_exist_and_no_default_decoder_is_set()
    {
        $manager = new DecoderManager();
        $manager->setDefaultType(null);
        $manager->getDecoder('dummy');
    }

    function test_getDecoder_uses_default_decoder_when_the_decoder_does_not_exist()
    {
        $manager = new DecoderManager([], 'json');
        $this->assertInstanceOf(DecoderInterface::class, $manager->getDecoder('dummy'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function test_getDecoder_fails_if_no_default_exist()
    {
        $manager = new DecoderManager([], 'json');
        $manager->setDefaultType(null);
        $manager->getDecoder('dummy');
    }

    function test_can_register_decoder()
    {
        $decoder  = new JsonDecoder();
        $manager = new DecoderManager();
        $manager->registerDecoder('dummy', $decoder);
        $this->assertSame($decoder, $manager->getDecoder('dummy'));
    }

    function test_it_doesnt_use_defaults_if_decoders_are_provided()
    {
        $decoders  = [
            'dummy' => new JsonDecoder()
        ];

        $manager = new DecoderManager($decoders);

        $this->assertFalse($manager->hasDecoder('json'));
        $this->assertTrue($manager->hasDecoder('dummy'));
    }
}
