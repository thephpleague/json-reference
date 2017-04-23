<?php

namespace League\JsonReference\Test\ReferenceSerializer;

use League\JsonReference\Dereferencer;
use League\JsonReference\ReferenceSerializationException;
use League\JsonReference\ReferenceSerializer\InlineReferenceSerializer;

class InlineReferenceSerializerTest extends \PHPUnit_Framework_TestCase
{
    function test_it_inlines_inline_references()
    {
        $deref  = (new Dereferencer())->setReferenceSerializer(new InlineReferenceSerializer());
        $path   = 'file://' . __DIR__ . '/../fixtures/inline-ref.json';
        $result = json_decode(json_encode($deref->dereference($path)), true);

        $this->assertSame('object', $result['properties']['billing_address']['type']);
    }

    function test_it_inlines_remote_references()
    {
        $deref  = (new Dereferencer())->setReferenceSerializer(new InlineReferenceSerializer());
        $result = json_decode(json_encode($deref->dereference('http://localhost:1234/album.json')), true);
        $this->assertSame('integer', $result['properties']['stars']['type']);
    }

    function test_it_throws_when_serializing_circular_references()
    {
        if (PHP_MAJOR_VERSION < 7) {
            $this->expectException(\Exception::class);
        } else {
            $this->expectException(ReferenceSerializationException::class);
        }
        $deref  = (new Dereferencer())->setReferenceSerializer(new InlineReferenceSerializer());
        $path   = 'file://' . __DIR__ . '/../fixtures/circular-ref-self.json';
        json_encode($deref->dereference($path));
    }

    function test_it_does_not_throw_when_serializing_indirect_circular_references()
    {
        $deref  = (new Dereferencer())->setReferenceSerializer(new InlineReferenceSerializer());
        $path   = 'file://' . __DIR__ . '/../fixtures/circular-ref-indirect.json';
        $this->assertFalse(json_encode($deref->dereference($path)));
        $this->assertSame(JSON_ERROR_RECURSION, json_last_error());
    }
}
