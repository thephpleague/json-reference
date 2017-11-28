<?php

namespace League\JsonReference\Loader;

use League\JsonReference\DecoderManager;
use League\JsonReference\DecoderInterface;
use League\JsonReference\LoaderInterface;
use League\JsonReference\SchemaLoadingException;
use function League\JsonReference\determineMediaType;

final class FileGetContentsWebLoader implements LoaderInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var DecoderManager
     */
    private $decoderManager;

    /**
     * @param string $prefix
     * @param DecoderInterface|DecoderManager $decoders
     */
    public function __construct($prefix, $decoderManager = null)
    {
        $this->prefix = $prefix;

        if ($decoderManager instanceof DecoderInterface) {
            $this->decoderManager = new DecoderManager([null => $decoderManager]);
        } else {
            $this->decoderManager = $decoderManager ?: new DecoderManager();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        $uri = $this->prefix . $path;
        set_error_handler(function () use ($uri) {
            throw SchemaLoadingException::create($uri);
        });
        $response = file_get_contents($uri);
        restore_error_handler();

        if (!$response) {
            throw SchemaLoadingException::create($uri);
        }

        $headers = $this->parseHttpResponseHeader($http_response_header);
        $type    = determineMediaType(['headers' => $headers, 'uri' => $uri]);
        return $this->decoderManager->getDecoder($type)->decode($response);
    }

    /**
     * Parse http headers returned by $http_response_header
     * @link http://php.net/manual/en/reserved.variables.httpresponseheader.php
     *
     * @param array $headers
     *
     * @return array
     */
    public static function parseHttpResponseHeader($headers)
    {
        $head = array();
        foreach ($headers as $k => $v) {
            $t = explode(':', $v, 2);
            if (isset($t[1])) {
                $head[ trim($t[0]) ] = trim($t[1]);
            } else {
                $head[] = $v;
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out)) {
                    $head['response_code'] = intval($out[1]);
                }
            }
        }

        return $head;
    }
}
