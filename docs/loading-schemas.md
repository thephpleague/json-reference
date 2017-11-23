---
layout: default
title: Loading Schemas
---

# Loaders

Anytime a schema is loaded it uses a loader.  Loaders are registered for a specific URI scheme (the part before `://`, like `http`).  You need to register a loader for every URI scheme you would like to load references for.

Loaders can also be decorated to add behavior like caching.

## Default Loaders

By default loaders are registered for the `file`, `http`, and `https` protocols.  The web loaders will use curl if available and fall back to a `file_get_contents` loader.

## Available Loaders

### File Loader

Loads schemas from the local filesystem.  Automatically registered for the `file` scheme.

### Curl Loader

Loads schemas using curl.  This loader is automatically registered for the `http` and `https` schemes if the curl extension is available.

### File Get Contents Web Loader

Loads remote schemas using `file_get_contents`.  This loader will be used for the `http` and `https` protocols if the curl extension is not available.

### Array Loader

The Array loader loads schemas from an array.  Useful for testing or limiting the possible schemas to a defined set.

```
<?php

$schemas = [
    'user' => json_decode('{ "properties": { "name" : { "type": "string" } } }')
];
$loader = new ArrayLoader($schemas);
```

### Chained Loader

The chained loader takes two other loaders as constructor parameters, and will attempt to load from the first loader before deferring to the second loader.

This is useful if you would like to register multiple loaders from the same prefix.  For instance, you may want to load a specific url from the local filesystem while loading all other schemas via http.

```php
<?php

use \ActiveRules\JsonReference\Loader\ArrayLoader;
use \ActiveRules\JsonReference\Loader\ChainedLoader;
use \ActiveRules\JsonReference\Loader\CurlWebLoader;

$loader = new ChainedLoader(
    new ArrayLoader(['json-schema.org/draft-04/schema' => json_decode(__DIR__ . '/schema.json')]),
    new CurlWebLoader('http')
);
```

### Cached Loader

The cached loader takes a [PSR-16 Simple Cache](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-16-simple-cache.md) implementation and another loader as constructor dependencies.  When a schema is loaded it will be returned from cache if available.  Otherwise it will be loaded using the decorated loader and cached.  The cached loader is used automatically when using the cached dereferencer.

## Custom Loaders

You can make your own loaders by implementing the [Loader Interface](https://github.com/thephpleague/json-reference/blob/master/src/LoaderInterface.php).

Imagine you may want to load schemas from a CouchDb database, and your references look like this:

```json
{ "$ref":"couchdb://00a271787f89c0ef2e10e88a0c0001f4" }
```

You could write a loader like this:

```php
class CouchDbLoader
{
    // constructor, etc

    public function load($path)
    {
        $response = $this->couchDbClient->findDocument($path);
        if ($response->status === 404) {
            throw SchemaLoadingException::notFound($path);
        }
        return $response->body;
    }
}

Once you have written your custom loader, you can register it.

## Registering Loaders

Loaders are registered with the dereferencer's LoaderManager.  You register a loader by passing the scheme you would like to load schemas for and the loader instance to the `registerLoader` method.

```php
<?php

use My\App\CustomLoader;

$customLoader = new CustomLoader();
$deref  = new ActiveRules\JsonReference\Dereferencer();

$deref->getLoaderManager()->registerLoader('http', $customLoader);
$deref->getLoaderManager()->registerLoader('https', $customLoader);
```
