---
layout: default
title: Relative References
---

# Relative References

When the dereferencer encounters a reference, the reference is resolved like a URI.  For example:

| Base                           | Reference                       | Resolved                        |
| -------------------------------|---------------------------------|---------------------------------|
| http://app.dev/api/schema.json | user.json                       | http://app.dev/api/user.json    |
| http://app.dev/api/schema.json | ../user.json                    | http://app.dev/user.json        |
| http://app.dev/api/schema.json | http://app.dev/api/v2/user.json | http://app.dev/api/v2/user.json |

This means you can use relative references and they will mostly work like you expect.  You are also free to use absolute references.  The reference is always resolved against the URI of the current schema.

## JSON Schema IDs

In JSON Schema [the id property](https://spacetelescope.github.io/understanding-json-schema/structuring.html#the-id-property) overrides the base URI used to resolve references.  Lets say you load the following schema from `file:///app/schemas/albums.json`:

```json
{
  "id": "http://app.dev/api/albums.json"
  "type": "array",
  "items": {
    "$ref": "./album.json"
  }
}
```

The dereferencer will attempt to load `./album.json` from `http://app.dev/api/album.json`, **not** `file:///app/schemas/album.json`, even though that is where you actually loaded the schema from.

To enable JSON Schema id resolution you can use the named constructor that corresponds to the version of JSON Schema you are using.  Draft 4 uses `id`, Draft 6 uses `$id`.

```php
$dereferencer = League\JsonReference\Dereferencer::draft4();
```
