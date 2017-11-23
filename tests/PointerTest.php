<?php

namespace Activerules\JsonReference\Test;

use Activerules\JsonReference\Pointer;

class PointerTest extends \PHPUnit_Framework_TestCase
{
    function test_get()
    {
        $document = $this->getDocument();
        $pointer = new Pointer($document);

        $this->assertCorrectJson($document, $pointer->get(''));
        $this->assertCorrectJson($document->foo, $pointer->get('/foo'));
        $this->assertCorrectJson('bar', $pointer->get('/foo/0'));
        $this->assertCorrectJson('baz', $pointer->get('/foo/1'));
        $this->assertCorrectJson(0, $pointer->get('/'));
        $this->assertCorrectJson(1, $pointer->get('/a~1b'));
        $this->assertCorrectJson(2, $pointer->get('/c%d'));
        $this->assertCorrectJson(3, $pointer->get('/e^f'));
        $this->assertCorrectJson(4, $pointer->get('/g|h'));
        $this->assertCorrectJson(5, $pointer->get('/i\\j'));
        $this->assertCorrectJson(6, $pointer->get("/k\"l"));
        $this->assertCorrectJson(7, $pointer->get('/ '));
        $this->assertCorrectJson(8, $pointer->get('/m~0n'));
        // url encoded
        $this->assertCorrectJson(2, $pointer->get('#/c%25d'));
        $this->assertCorrectJson(3, $pointer->get('#/e^f'));
        $this->assertCorrectJson(4, $pointer->get('#/g%7Ch'));
        $this->assertCorrectJson(5, $pointer->get('#/i%5Cj'));
        $this->assertCorrectJson(6, $pointer->get("#/k%22l"));
        $this->assertCorrectJson(7, $pointer->get('#/%20'));
        $this->assertCorrectJson(8, $pointer->get('#/m~0n'));
    }

    function test_set()
    {
        $document = $this->getDocument();
        $pointer = new Pointer($document);

        $pointer->set('/foo', [1,2,3,4]);
        $this->assertSame($document->foo, [1,2,3,4]);
    }

    function test_set_works_with_arrays()
    {
        $document = $this->getDocument();
        $pointer = new Pointer($document);

        $pointer->set('/foo/0', 'oranges');
        $this->assertSame('oranges', $document->foo[0]);
    }

    function test_set_can_set_new_element_in_array()
    {
        $document = $this->getDocument();
        $pointer = new Pointer($document);

        $pointer->set('/foo/-', 'bam');
        $this->assertSame('bam', $document->foo[2]);
    }

    function test_set_when_the_path_is_inside_an_array()
    {
        // /properties/type/anyOf/1/items
        $document = $this->getDocument();
        $pointer = new Pointer($document);
        $pointer->set('/nested/0/type', 'boolean');
        $this->assertInternalType('array', $document->nested);
        $this->assertCount(2, $document->nested);
        $this->assertSame('boolean', $document->nested[0]->type);
    }

    function test_get_throws_when_the_pointer_is_not_a_string()
    {
        $this->setExpectedException(Pointer\InvalidPointerException::class);
        $document = $this->getDocument();
        $pointer = new Pointer($document);
        $pointer->get(['bad' => 'type']);
    }

    function test_get_throws_when_the_property_is_not_in_a_container()
    {
        $this->setExpectedException(Pointer\InvalidPointerException::class);
        $document = $this->getDocument();
        $pointer = new Pointer($document);
        $pointer->get('/foo/0/invalid');
    }

    function test_set_throws_when_the_property_is_not_in_a_container()
    {
        $this->setExpectedException(Pointer\InvalidPointerException::class);
        $document = $this->getDocument();
        $pointer = new Pointer($document);
        $pointer->set('/foo/0/invalid', []);
    }

    function test_get_throws_when_the_pointer_does_not_start_with_a_slash()
    {
        $this->setExpectedException(Pointer\InvalidPointerException::class);
        $document = $this->getDocument();
        $pointer = new Pointer($document);
        $pointer->get('#hello/world');
    }

    function test_get_throws_when_accessing_an_invalid_array_index()
    {
        $this->setExpectedException(Pointer\InvalidPointerException::class);
        $document = $this->getDocument();
        $pointer = new Pointer($document);
        $pointer->get('/foo/5');
    }

    function test_cannot_replace_an_entire_object_with_set()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $document = $this->getDocument();
        $pointer = new Pointer($document);
        $pointer->set('', []);
    }

    function test_get_can_traverse_an_empty_property()
    {
        // This was fixed in PHP 7.1 so we are going to simulate it in the test.
        // @see https://github.com/php/php-src/pull/1926/commits/f0d1cca6729f2593900af10d6aa324b7eedfe0c3
        $document = (object) ['_empty_' => ['bar' => 'baz']];
        $pointer  = new Pointer($document);
        $this->assertSame('baz', $pointer->get('//bar'));
    }

    function test_remove_removes_objects()
    {
        $document = $this->getDocument();
        $pointer = new Pointer($document);
        $pointer->remove('/nested/0');
        $this->assertCount(1, $document->nested);
    }

    function test_remove_removes_arrays()
    {
        $document = $this->getDocument();
        $pointer = new Pointer($document);
        $pointer->remove('/foo');
        $this->assertFalse(property_exists($document, 'foo'));
    }

    function test_remove_removes_properties()
    {
        $document = $this->getDocument();
        $pointer = new Pointer($document);
        $pointer->remove('/a');
        $this->assertFalse(property_exists($document, 'a'));
    }

    protected function assertCorrectJson($expected, $actual, $message = '')
    {
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual), $message);
    }

    protected function getDocument()
    {
        return json_decode(file_get_contents(__DIR__ . '/fixtures/pointer.json'));
    }
}
