---
layout: default
title: Decoding
---

# Decoders

Anytime a schema is decoded it uses a decoder. Decoders are registered for a specific file extension. You need to register the proper decoder for the file type you would like to decode. 

Decoders can also be decorated to add behavior like caching.

## Default Decoders

By default the 'json' decoder is used.

## Available Decoders

- Json
- Yaml

## Custom Decoders

You can make your own decoders by implementing the [Decoder Interface](https://github.com/thephpleague/json-reference/blob/master/src/DecoderInterface.php).

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

Decoders are registered in the Loaders's constructor. You register a decoder by passing an instance.

```php
<?php

use My\App\CustomDecoder;

$decoder = new CustomDecoder();
$loader  = new FileLoader($decoder);
$deref   = new League\JsonReference\Dereferencer();

$deref->getLoaderManager()->registerLoader('file', $loader);
```
