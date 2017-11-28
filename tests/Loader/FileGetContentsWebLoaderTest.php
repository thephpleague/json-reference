<?php

namespace League\JsonReference\Test\Loader;

use League\JsonReference\Loader\FileGetContentsWebLoader;

class FileGetContentsWebLoaderTest extends \PHPUnit_Framework_TestCase
{
    function test_it_loads_schemas()
    {
        $loader = new FileGetContentsWebLoader('http://');
        $response = $loader->load('localhost:1234/integer.json');
        $this->assertSame('{"type":"integer"}', json_encode($response));
    }

    /**
     * @expectedException \League\JsonReference\SchemaLoadingException
     */
    function test_it_throws_when_the_schema_is_not_found()
    {
        $loader = new FileGetContentsWebLoader('http://');
        $loader->load('localhost:1234/unknown');
    }

    /**
     * @expectedException \League\JsonReference\SchemaLoadingException
     */
    function test_it_throws_when_the_response_is_empty()
    {
        $loader = new FileGetContentsWebLoader('http://');
        $loader->load('localhost:1234/empty.json');
    }
    
    function test_constructor_accepts_decoder_interface() 
    {
        $decoder  = new \League\JsonReference\Decoder\YamlDecoder;
        $loader   = new FileGetContentsWebLoader('http://', $decoder);
        $response = $loader->load('localhost:1234/string.yaml');

        $this->assertEquals((object) ['type'=>'string'], $response);
    }
    
    function headers()
    {
        return [
            [
                [
                    'HTTP/1.1 200 OK',
                    'Date: Sat, 12 Apr 2008 17:30:38 GMT',
                    'Server: Apache/2.2.3 (CentOS)',
                    'Last-Modified: Tue, 15 Nov 2005 13:24:10 GMT',
                    'ETag: 280100-1b6-80bfd280',
                    'Accept-Ranges: bytes',
                    'Content-Length: 438',
                    'Connection: close',
                    'Content-Type: text/html; charset=UTF-8'
                ],
                [
                    0 => 'HTTP/1.1 200 OK',
                    'response_code' => 200,
                    'Date' => 'Sat, 12 Apr 2008 17:30:38 GMT',
                    'Server' => 'Apache/2.2.3 (CentOS)',
                    'Last-Modified' => 'Tue, 15 Nov 2005 13:24:10 GMT',
                    'ETag' => '280100-1b6-80bfd280',
                    'Accept-Ranges' => 'bytes',
                    'Content-Length' => '438',
                    'Connection' => 'close',
                    'Content-Type' => 'text/html; charset=UTF-8'
                ]
            ]
        ];
    }

    /**
     * @dataProvider headers
     */
    function test_it_parses_headers($header, $expectedResult)
    {
        $this->assertEquals($expectedResult, FileGetContentsWebLoader::parseHttpResponseHeader($header));
    }
}
