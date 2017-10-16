---
layout: default
title: Decoding
---

# Decoders

Anytime a schema is decoded it uses a decoder.  Decoders are registered for a specific file extension.  You need to register a decoder for every file type you would like to decode. 

Decoders can also be decorated to add behavior like caching.

## Default Decoders

By default decoders are registered for the file extensions `json`, `yaml`, and `yml` protocols. 

## Available Decoders

### Json Decoder

Decodes schemas from json.

### Yaml Decoder

Decodes schemas from Yaml.

## Custom Decoders

You can make your own decoders by implementing the [Decoder Interface](https://github.com/thephpleague/json-reference/blob/master/src/DecoderInterface.php).

Imagine you may want to decode schemas from a xml document, and your references look like this:

```json
{ "$ref":"schema.xml" }
```

You could write a decoder like this:

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

$customDecoder = new CustomDecoder();
$deref  = new League\JsonReference\Dereferencer();

$deref->getLoaderManager()->getDecoderManager()->registerDecoder('xml', $customDecoder);
```
