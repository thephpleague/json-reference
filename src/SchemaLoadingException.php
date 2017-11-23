<?php

namespace Activerules\JsonReference;

final class SchemaLoadingException extends \RuntimeException
{
    public static function create($path)
    {
        return new static(sprintf('The schema "%s" could not be loaded.', $path));
    }

    public static function notFound($path)
    {
        return new static(sprintf('The schema "%s" could not be found.', $path));
    }
}
