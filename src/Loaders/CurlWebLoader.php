<?php

namespace League\JsonReference\Loaders;

use League\JsonReference;
use League\JsonReference\JsonDecoder;
use League\JsonReference\JsonDecoders\StandardJsonDecoder;
use League\JsonReference\Loader;

final class CurlWebLoader implements Loader
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var array
     */
    private $curlOptions;

    /**
     * @var JsonDecoder
     */
    private $jsonDecoder;

    /**
     * @param string      $prefix
     * @param array       $curlOptions
     * @param JsonDecoder $jsonDecoder
     */
    public function __construct($prefix, array $curlOptions = null, JsonDecoder $jsonDecoder = null)
    {
        $this->prefix      = $prefix;
        $this->jsonDecoder = $jsonDecoder ?: new StandardJsonDecoder();
        $this->setCurlOptions($curlOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        $uri = $this->prefix . $path;
        $ch = curl_init($uri);
        curl_setopt_array($ch, $this->curlOptions);
        list($response, $statusCode) = $this->getResponseBodyAndStatusCode($ch);
        curl_close($ch);

        if ($statusCode >= 400 || !$response) {
            throw JsonReference\SchemaLoadingException::create($uri);
        }

        return $this->jsonDecoder->decode($response);
    }

    /**
     * @param resource $ch
     *
     * @return array
     */
    private function getResponseBodyAndStatusCode($ch)
    {
        $response   = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return [$response, $statusCode];
    }

    /**
     * @return array
     */
    private function getDefaultCurlOptions()
    {
        return [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 20,
        ];
    }

    /**
     * @param array|null $curlOptions
     */
    private function setCurlOptions($curlOptions)
    {
        if (is_array($curlOptions)) {
            $this->curlOptions = $curlOptions + $this->getDefaultCurlOptions();
            return;
        }

        $this->curlOptions = $this->getDefaultCurlOptions();
    }
}
