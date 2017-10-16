<?php

namespace League\JsonReference\Test\Loader;

use League\JsonReference\Decoder\JsonDecoder;
use League\JsonReference\DecodingException;

class JsonDecoderTest extends \PHPUnit_Framework_TestCase
{
    function test_it_decodes()
    {
        $schema =  <<<'JSON'
{
    "type": "object",
    "properties": {
        "street_address": { "type": "string" },
        "city":           { "type": "string" },
        "state":          { "type": "string" }
    },
    "required": ["street_address", "city", "state"]
}
JSON;

        $expectedObject = (object) [
            'type' => 'object',
            'properties' => (object) [
                'street_address' => (object) [ 'type' => 'string' ],
                'city'           => (object) [ 'type' => 'string' ],
                'state'          => (object) [ 'type' => 'string' ],
            ],
            'required' => ['street_address', 'city', 'state']
        ];

        $decoder = new JsonDecoder();
        $this->assertEquals($expectedObject, $decoder->decode($schema));
    }
}
