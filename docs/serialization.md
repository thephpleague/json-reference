---
layout: default
title: Serialization
---

# Serialization

It would be great if you could `json_encode` the dereferenced JSON and get back the object with all of the referenced JSON inlined.  Unfortunately there might be [circular references](./circular-references) so that isn't always possible.

When you `json_encode` a dereferenced JSON document the default serializer transforms all references into the original `{ "$ref": "#/some/reference" }` format instead of attempting to inline them

## Inlining References

If you know your document does not have any circular references you can inline the referenced JSON.

The `InlineReferenceSerializer` will attempt to inline references and throw an exception if a direct circular reference is found.  An indirect circular reference may still exist, in which case `json_encode` will fail and `json_last_error` will return `JSON_ERROR_RECURSION`.

```php
$dereferencer = new Dereferencer();
$dereferencer->setReferenceSerializer(new InlineReferenceSerializer());

$schema = json_encode($dereferencer->dereference('file:///app/schemas/schema.json'));
```

## Custom Serializers

You can use your own serializer by implementing the `ReferenceSerializerInterface`.
