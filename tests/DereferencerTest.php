<?php

namespace League\JsonReference\Test;

use League\JsonReference\Dereferencer;
use League\JsonReference\Loader\ArrayLoader;
use League\JsonReference\Pointer;
use League\JsonReference\Reference;
use function peterpostmann\uri\fileuri;

class DereferencerTest extends \PHPUnit_Framework_TestCase
{
    function test_it_resolves_inline_references()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/inline-ref.json', __DIR__);
        $result = $deref->dereference($path);

        $this->assertSame(json_encode($result->definitions->address), json_encode($result->properties->billing_address->resolve()));
        $this->assertSame(json_encode($result->definitions->address), json_encode($result->properties->shipping_address->resolve()));
    }

    function test_it_resolves_inline_references_when_initial_schema_used_pointer()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/inline-ref.json', __DIR__).'#/properties/billing_address';
        $result = $deref->dereference($path);

        $expected = json_decode(file_get_contents(__DIR__ . '/fixtures/inline-ref.json'));
        $expected = (new Pointer($expected))
            ->get('/definitions/address');

        $this->assertEquals($expected, $result);
    }

    function test_it_resolves_remote_references()
    {
        $loader = new ArrayLoader(
            ['json-schema.org/draft-04/schema' => file_get_contents(__DIR__ . '/fixtures/draft4-schema.json')]
        );
        $deref  = new Dereferencer();
        $deref->getLoaderManager()->registerLoader('http', $loader);
        $deref->getLoaderManager()->registerLoader('https', $loader);
        $result = $deref->dereference('http://json-schema.org/draft-04/schema#');
        $this->assertSame($result->definitions->positiveIntegerDefault0, $result->properties->minItems->resolve());
    }

    function test_it_resolves_remote_references_without_an_id()
    {
        $deref  = new Dereferencer();
        $result = $deref->dereference('http://localhost:1234/albums.json');

        $this->assertSame('string', $result->items->properties->title->type);
    }
    
    function test_it_resolves_mixed_references()
    {
        $deref  = new Dereferencer();
        $deref->getLoaderManager()->getDecoderManager()->registerYamlDecoder();
        $result = $deref->dereference('http://localhost:1234/albums.yaml');

        $this->assertSame('string', $result->items->properties->title->type);
    }

    function test_it_fails_when_resolving_a_remote_reference_without_id_or_uri()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $deref  = new Dereferencer();
        $deref->dereference(json_decode('{"$ref": "album.json"}'));
    }

    function test_it_resolves_web_remote_references_with_fragments()
    {
        $deref  = new Dereferencer();
        $result = $deref->dereference('http://localhost:1234/subSchemas.json#/relativeRefToInteger');
        $this->assertSame(['type' => 'integer'], (array) $result);
    }

    function test_it_resolves_file_remote_references_with_fragments()
    {
        $deref  = new Dereferencer();
        $path = fileuri('fixtures/schema.json', __DIR__).'#/properties';
        $result = $deref->dereference($path);
        $this->assertArrayHasKey('name', (array) $result);
    }

    function test_it_resolves_recursive_root_pointers()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/recursive-root-pointer.json', __DIR__);
        $result = $deref->dereference($path);
        $this->assertSame(
            $result->properties->foo->additionalProperties,
            $result->properties->foo->properties->foo->additionalProperties
        );
    }

    function test_it_resolves_circular_references_to_self()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/circular-ref-self.json', __DIR__);
        $result = $deref->dereference($path);

        $this->assertSame(
            '{"$ref":"#\/definitions\/thing"}',
            json_encode($result->definitions->thing)
        );
        $this->assertSame(
            $result->definitions->thing,
            $result->definitions->thing->resolve()->resolve()->resolve()->resolve(),
            'You should be able to resolve recursive definitions to any depth'
        );
    }

    function test_it_resolves_circular_references_to_parent()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/circular-ref-parent.json', __DIR__);
        $result = $deref->dereference($path);
        $ref    = $result
            ->definitions
            ->person
            ->properties
            ->spouse
            ->type
            ->resolve();

        $this->assertSame(json_encode($result->definitions->person), json_encode($ref));
    }

    function test_it_resolves_indirect_circular_references()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/circular-ref-indirect.json', __DIR__);
        $result = $deref->dereference($path);

        $this->assertSame(
            $result->definitions->parent->properties->children->items->properties->name,
            $result->definitions->child->properties->name
        );
    }

    function test_resolves_references_in_arrays()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/array-ref.json', __DIR__);
        $result = $deref->dereference($path);
        $this->assertSame($result->items[0], $result->items[1]->resolve());
    }

    function test_dereferences_properties_that_begin_with_a_slash()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/slash-property.json', __DIR__);
        $result = $deref->dereference($path);
        $slashProperty = '/slash-item';
        $this->assertSame($result->$slashProperty->key, $result->item->key);
    }

    function test_it_dereferences_properties_with_tilde_in_name()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/tilde-property.json', __DIR__);
        $result = $deref->dereference($path);
        $tildeProperty = 'tilde~item';
        $this->assertSame($result->$tildeProperty->key, $result->item->key);
    }

    function test_it_ignores_references_that_are_not_strings()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/property-named-ref.json', __DIR__);
        $result = $deref->dereference($path);

        $this->assertTrue(is_object($result->properties->{'$ref'}));
        $this->assertSame($result->properties->{'$ref'}->description, 'The name of the property is $ref, but it\'s not a reference.');
    }

    function test_it_resolves_relative_scope_against_an_id()
    {
        $deref = Dereferencer::draft4();
        $result = $deref->dereference(json_decode('{"id": "http://localhost:1234/test.json", "properties": {"album": {"$ref": "album.json"}}}'));
        $this->assertSame('object', $result->properties->album->type);
    }

    function test_it_resolves_circular_external_references()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/circular-ext-ref.json', __DIR__);
        $result = $deref->dereference($path);
        $this->assertInstanceOf(Reference::class, $result->properties->rating);
        $this->assertFalse($result->properties->rating->additionalProperties);
        $this->assertFalse($result->properties->rating->properties->rating->additionalProperties);
    }

    function test_it_returns_serializable_schemas()
    {
        $deref  = new Dereferencer();
        $path   = fileuri('fixtures/inline-ref.json', __DIR__);
        $result = $deref->dereference($path);

        $this->assertEquals($result, unserialize(serialize($result)));
    }
}
