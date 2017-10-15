<?php

namespace League\JsonReference\Test\Loader;

use League\JsonReference\Decoder\YamlDecoder;
use League\JsonReference\DecodingException;

class YamlDecoderTest extends \PHPUnit_Framework_TestCase
{
    function test_it_decodes()
    {
        $schema = <<<'YAML'
type: object
properties: 
    street_address: 
        type: string
    city:
        type: string
    state:
        type: string
required: 
    - street_address
    - city
    - state
YAML;

        $expectedObject = (object) [
            'type' => 'object',
            'properties' => (object) [
                'street_address' => (object) [ 'type' => 'string' ],
                'city'           => (object) [ 'type' => 'string' ],
                'state'          => (object) [ 'type' => 'string' ],
            ],
            'required' => ['street_address', 'city', 'state']
        ];

        $decoder = new YamlDecoder();
        $this->assertEquals($expectedObject, $decoder->decode($schema));
    }
}
