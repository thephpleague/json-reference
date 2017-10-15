<?php

namespace League\JsonReference\Loader;

use League\JsonReference;
use League\JsonReference\DecoderManager;
use League\JsonReference\DecoderInterface;
use League\JsonReference\LoaderInterface;

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
    private $decoders;

    /**
     * @param string                              $prefix
     * @param array                               $curlOptions
     * @param JsonDecoderInterface|DecoderManager $decoders
     */
    public function __construct($prefix, array $curlOptions = null, $decoders = null)
    {
        $this->prefix = $prefix;
        $this->setCurlOptions($curlOptions);
        
        if ($decoders instanceof DecoderInterface) {
            $this->decoders = new DecoderManager([$decoders]);
        } else {
            $this->decoders = $decoders ?: new DecoderManager();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($path, $defaultExtension = 'json')
    {
        $uri = $this->prefix . $path;
        
        $extension = isset(pathinfo($path)['extension']) ? pathinfo($path)['extension'] : $defaultExtension;
        
        $ch = curl_init($uri);
        curl_setopt_array($ch, $this->curlOptions);
        list($response, $statusCode) = $this->getResponseBodyAndStatusCode($ch);
        curl_close($ch);

        if ($statusCode >= 400 || !$response) {
            throw JsonReference\SchemaLoadingException::create($uri);
        }

        return $this->decoders->getDecoder($extension)->decode($response);
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
