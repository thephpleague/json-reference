<?php

namespace League\JsonReference\Test;

class DeprecatedTest extends \PHPUnit_Framework_TestCase
{
    function test_json_decoder_interface_exists()
    {
        $this->assertTrue(interface_exists('League\JsonReference\JsonDecoderInterface'));
    }

    function test_json_decoding_exception_exists()
    {
        $this->assertTrue(class_exists('League\JsonReference\JsonDecodingException'));
    }
    
    function test_json_decoder_exists()
    {
        $this->assertTrue(class_exists('League\JsonReference\JsonDecoder\JsonDecoder'));
    }
}
