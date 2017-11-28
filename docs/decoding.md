---
layout: default
title: Decoding
---

# Decoders

Anytime a schema is decoded it uses a decoder. Decoders are registered for a specific media type. You need to register a decoder for every type you would like to decode. 

Decoders can also be decorated to add behavior like caching.

## Default Decoders

By default the 'json' decoder is used.

## Available Decoders

- Json
- Yaml

## Custom Decoders

You can create your own decoders by implementing the [Decoder Interface](https://github.com/thephpleague/json-reference/blob/master/src/DecoderInterface.php).

Imagine you may want to decode schemas from a xml document, and your references look like this:

```json
{ "$ref":"schema.xml" }
```

You can write a decoder like this:

```php
class CustomDecoder
{
    public function decode($schema)
    {
        try {
            return CustomParser($schema);
        } catch (CustomParseException $e) {
            throw new DecodingException(sprintf('Invalid Syntax: %s', $e->getMessage()), $e->getCode(), $e);
        }
    }
}
```

Once you have written your custom decoder, you can register it.

## Registering Decoders

Decoders are registered with the Loaders's DecoderManager. You register a decoder by passing the extension you would like to decode schemas for and the decoder instance to the `registerDecoder` method.

```php
<?php

use My\App\CustomDecoder;

$decoder = new CustomDecoder();
$loader  = new FileLoader($decoder);
$deref   = new League\JsonReference\Dereferencer();

$deref->getLoaderManager()->getDecoderManager()->registerDecoder('xyz', $customDecoder);
```

### Media Types

The decoder manager determines the decoder based on the Content-Type header (first priority) or file extension (second priority). 


| Content-Type Header | File Extension | Evaluated Type |
| ------------------- | -------------- | -------------- |
| xxx/yyy             |                | xxx/yyy        |
| xxx/yyy+zzz         |                | +zzz           |
| xxx/yyy+zzz         | foo.bar        | +zzz           |
| xxx/yyy             | foo.bar        | xxx/yyy        |
|                     | foo.bar        | bar            |
|                     |                | null           |

If the suffix of a sub-type is used, a '+'-sign is used to distinguishe between suffixes and file extensions.
