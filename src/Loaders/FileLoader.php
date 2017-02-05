<?php

namespace League\JsonReference\Loaders;

use League\JsonReference;
use League\JsonReference\Loader;
use League\JsonReference\SchemaLoadingException;

final class FileLoader implements Loader
{
    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        if (!file_exists($path)) {
            throw SchemaLoadingException::notFound($path);
        }

        return JsonReference\json_decode(file_get_contents($path), false, 512, JSON_BIGINT_AS_STRING);
    }
}
