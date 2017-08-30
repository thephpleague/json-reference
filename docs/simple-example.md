---
layout: default
title: Simple Example
---

# Simple Example

Let's say you have a JSON document like this:

```json
{
  "definitions": {
    "pet": {
      "type": "object",
      "properties": {
        "name":  { "type": "string" },
        "breed": { "type": "string" },
        "age":  { "type": "string" }
      },
      "required": ["name", "breed", "age"]
    }
  },
  "type": "object",
  "properties": {
    "cat": { "$ref": "#/definitions/pet" },
    "dog": { "$ref": "#/definitions/pet" }
  }
}
```

This document only has _internal_ references.  Internal references use a [JSON Pointer](https://tools.ietf.org/html/rfc6901) and start with an anchor (`#`) character.  We want to resolve the references `#/definitions/pet` and replace them with the JSON value at that location in the schema.

## Usage

To dereference your schema, create a new `Dereferencer` instance.

```php
<?php

$dereferencer  = new League\JsonReference\Dereferencer();
```

Now call the `dereference` method with the path to your schema.

```php
<?php

$schema = $dereferencer->dereference('file://' . __DIR__ . '/pets.json');
```

<div class="message-info">
  The schema was loaded with a [File URI](https://en.wikipedia.org/wiki/File_URI_scheme).  By default `http://`, `https://`, and `file://` URIs are supported.  You need to load the schema from a URI to be able to resolve relative references like `./pets/cat.json`
</div>

The resulting object is identical, but references have been replaced with Reference objects.  Now you can work with the referenced schema like a regular JSON object.

```php
echo $schema->properties->cat->type; // echos 'object'
```

You can access the properties and iterate over them just like the rest of the schema.  If you `json_encode` it, you will get back the original schema.
