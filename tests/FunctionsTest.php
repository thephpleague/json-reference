<?php

namespace League\JsonReference\Test;

use function League\JsonReference\pointer_push;
use function League\JsonReference\parseContentTypeHeader;
use function League\JsonReference\determineMediaType;
use function League\JsonReference\parseHttpResponseHeader;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testUris()
    {
        return [
            // Technically the spec adds the superfluous # at the end, but we don't need to enforce that.
            ['http://x.y.z/rootschema.json#', '', 'http://x.y.z/rootschema.json#'],
            ['#foo', 'http://x.y.z/rootschema.json#', 'http://x.y.z/rootschema.json#foo'],
            ['otherschema.json', 'http://x.y.z/rootschema.json#', 'http://x.y.z/otherschema.json'],
            ['#bar', 'http://x.y.z/otherschema.json#', 'http://x.y.z/otherschema.json#bar'],
            ['t/inner.json#a', 'http://x.y.z/otherschema.json#', 'http://x.y.z/t/inner.json#a'],
            ['some://where.else/completely#', 'http://x.y.z/rootschema.json#', 'some://where.else/completely'],
            ['folderInteger.json', 'http://localhost:1234/folder/', 'http://localhost:1234/folder/folderInteger.json'],
            ['some-id.json', '', 'some-id.json'],
            ['item.json', 'http://some/where/other-item.json', 'http://some/where/item.json'],
            ['item.json', 'file:///schemas/other-item.json', 'file:///schemas/item.json'],
            ['#', 'file://x.y.z/schema.json', 'file://x.y.z/schema.json']
        ];
    }

    /**
     * @dataProvider testUris
     */
    function test_resolves_uris($id, $parentScope, $expectedResult)
    {
        $result = \League\JsonReference\resolve_uri($id, $parentScope);
        $this->assertSame($expectedResult, $result);
    }

    function test_schema_extract_extracts_matches()
    {
        $schema = json_decode('{ "properties": { "money": { "enum": [ { "currency": "USD" } ] } } }');

        $matches = \League\JsonReference\schema_extract($schema, function ($keyword, $value) {
            return $keyword === 'currency' && $value === 'USD';
        });

        $this->assertCount(1, $matches);
        $this->assertSame('/properties/money/enum/0', array_keys($matches)[0]);
        $this->assertSame('USD', reset($matches));
    }

    function test_pointer_push_handles_root_pointers()
    {
        $this->assertSame('/somewhere', pointer_push('/', 'somewhere'));
    }

    public function contentTypeHeaders()
    {
        return [
            [
                'text/html; charset=utf-8',
                [
                    'type'    => 'text',
                    'subtype' => 'html',
                    'suffix'  => null,
                    'parameter' => [
                        'charset' => 'utf-8'
                    ]
                ] 
            ],
            [
                'application/json',
                [
                    'type' => 'application',
                    'subtype' => 'json',
                    'suffix'  => null,
                    'parameter' => null
                ] 
            ],
            [
                'xxx/yyy+zzz',
                [
                    'type' => 'xxx',
                    'subtype' => 'yyy',
                    'suffix'  => 'zzz',
                    'parameter' => null
                ] 
            ],
            [
                'xxx/yyy+',
                [
                    'type' => 'xxx',
                    'subtype' => 'yyy',
                    'suffix'  => '',
                    'parameter' => null
                ] 
            ],
            [
                'teXt/HTML; CharSet=UtF-8',
                [
                    'type'    => 'text',
                    'subtype' => 'html',
                    'suffix'  => null,
                    'parameter' => [
                        'charset' => 'UtF-8'
                    ]
                ] 
            ],
        ];
    }

    /**
     * @dataProvider contentTypeHeaders
     */
    function test_content_type_parser($contentType, $expectedResult)
    {
        $this->assertSame($expectedResult, parseContentTypeHeader($contentType));
    }

    function mediaContext()
    {
        return [
            [['headers' => ['Content-Type' => 'xxx/yyy'    ],                   ], 'xxx/yyy'],
            [['headers' => ['Content-Type' => 'xxx/yyy+zzz'],                   ], '+zzz'],
            [['headers' => ['Content-Type' => 'xxx/yyy+zzz'], 'uri' => 'foo.bar'], '+zzz'],
            [['headers' => ['Content-Type' => 'xxx/yyy'    ], 'uri' => 'foo.bar'], 'xxx/yyy'],
            [[                                                'uri' => 'foo.bar'], 'bar'],
            [[                                                                  ], null],
            [[ 'uri' => 'example.org/foo.bar?a=b'                               ], 'bar'],
            [['headers' => ['Content-Type' => false        ], 'uri' => 'foo.bar'], 'bar'],
            [['headers' => ['Content-Type' => 'application/octet-stream'], 'uri' => 'foo.bar'], 'bar']
        ];
    }

    /**
     * @dataProvider mediaContext
     */
    function test_determineMediaType($mediaContext, $expectedResult) 
    {
        $this->assertSame($expectedResult, determineMediaType($mediaContext));
    }
}
