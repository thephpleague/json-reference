<?php

namespace League\JsonReference\Bench;

class SwaggerDereferenceBenchmark extends DereferenceBenchmark
{
    public function getSchema()
    {
        return 'file://' . __DIR__ . '/../fixtures/swagger2.json';
    }
}
