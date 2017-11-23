<?php

namespace Activerules\JsonReference\Bench;

class MetaSchemaDereferenceBench extends DereferenceBenchmark
{
    public function getSchema()
    {
        return 'file://' . __DIR__ . '/../fixtures/draft4-schema.json';
    }
}
