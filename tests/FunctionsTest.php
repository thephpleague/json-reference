<?php

namespace League\JsonReference\Test;

use function League\JsonReference\pointer_push;

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
            ['some://where.else/completely#', 'http://x.y.z/rootschema.json#', 'some://where.else/completely#'],
            ['folderInteger.json', 'http://localhost:1234/folder/', 'http://localhost:1234/folder/folderInteger.json'],
            ['some-id.json', '', 'some-id.json'],
            ['item.json', 'http://some/where/other-item.json', 'http://some/where/item.json'],
            ['item.json', 'file:///schemas/other-item.json', 'file:///schemas/item.json'],
            ['#', 'file://x.y.z/schema.json', 'file://x.y.z/schema.json#']
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
}
