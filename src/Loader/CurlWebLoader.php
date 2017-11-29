<?php

namespace League\JsonReference\Loader;

use League\JsonReference;
use League\JsonReference\DecoderManager;
use League\JsonReference\DecoderInterface;
use League\JsonReference\LoaderInterface;
use function League\JsonReference\determineMediaType;

final class CurlWebLoader implements LoaderInterface
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
     * @var DecoderManager
     */
    private $decoderManager;

    /**
     * @param string                          $prefix
     * @param array                           $curlOptions
     * @param DecoderInterface|DecoderManager $decoderManager
     */
    public function __construct($prefix, array $curlOptions = null, $decoderManager = null)
    {
        $this->prefix = $prefix;
        $this->setCurlOptions($curlOptions);
        
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
        $ch = curl_init($uri);
        curl_setopt_array($ch, $this->curlOptions);
        list($response, $statusCode, $type) = $this->getResponseBodyStatusCodeAndContentType($ch);
        curl_close($ch);
        
        if ($statusCode >= 400 || !$response) {
            throw JsonReference\SchemaLoadingException::create($uri);
        }

        $type = determineMediaType(['Content-Type' => $type, 'uri' => $uri]);
        return $this->decoderManager->getDecoder($type)->decode($response);
    }

    /**
     * @param resource $ch
     *
     * @return array
     */
    private function getResponseBodyStatusCodeAndContentType($ch)
    {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response    = curl_exec($ch);
        $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body        = substr($response, $header_size);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        return [$body, $statusCode, $contentType];
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
