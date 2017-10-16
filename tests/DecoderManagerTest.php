<?php

namespace League\JsonReference\Test;

use League\JsonReference\Decoder\JsonDecoder;
use League\JsonReference\Decoder\YamlDecoder;
use League\JsonReference\DecoderInterface;
use League\JsonReference\DecoderManager;

class DecoderManagerTest extends \PHPUnit_Framework_TestCase
{
    function test_can_get_all_decoders_indexed_by_prefix()
    {
        $manager = new DecoderManager();
        $decoders = $manager->getDecoders();
        $this->assertArrayHasKey('json', $decoders);
        $this->assertInstanceOf(DecoderInterface::class, $decoders['json']);
        $this->assertArrayHasKey('yaml', $decoders);
        $this->assertInstanceOf(DecoderInterface::class, $decoders['yaml']);
        $this->assertArrayHasKey('yml', $decoders);
        $this->assertInstanceOf(DecoderInterface::class, $decoders['yml']);
    }

    function test_getDecoder_uses_default_decoder_when_the_decoder_does_not_exist()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $manager = new DecoderManager([], false);
        $manager->getDecoder('xml');
    }

    function test_getDecoder_throws_when_the_decoder_does_not_exist_and_ignore_unknown_extension_is_true()
    {
        $manager = new DecoderManager([], true);
        $this->assertInstanceOf(DecoderInterface::class, $manager->getDecoder('dummy'));
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
        $this->assertFalse($manager->hasDecoder('yaml'));
        $this->assertFalse($manager->hasDecoder('yml'));
        $this->assertTrue($manager->hasDecoder('dummy'));
    }
}
