<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\Magento2RestApiConnector\Api
 * @copyright Copyright (c) 2022 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 06.09.2022
 */

namespace BroCode\Magento2RestApiConnector\Api;


use GuzzleHttp\Client;

/**
 *
 */
class Magento2ClientConfiguration
{
    /**
     * @var string
     */
    protected $baseUrl;
    /**
     * @var bool
     */
    protected $verify;
    /**
     * @var string
     */
    protected $headerAuthorization;
    /**
     * @var string
     */
    protected $headerUserAgent;
    /**
     * @var string
     */
    protected $headerContentType;
    /**
     * @var int
     */
    protected $concurrentRequests;

    /**
     * @var array
     */
    protected $additionalData = [];
    /**
     * Magento2ClientConfiguration constructor.
     * @param string $baseUrl
     * @param string $headerAuthorization
     * @param string $headerUserAgent
     * @param bool $verify
     * @param string $headerContentType
     */
    public function __construct(
        $baseUrl,
        $headerAuthorization,
        $headerUserAgent = 'Magento2 RestApi Connector',
        $concurrentRequests = 1,
        $verify = false,
        $headerContentType = 'application/json'
    ) {
        $this->baseUrl = $baseUrl;
        $this->headerAuthorization = $headerAuthorization;
        $this->headerUserAgent = $headerUserAgent;
        $this->verify = $verify;
        $this->headerContentType = $headerContentType;
        $this->concurrentRequests = $concurrentRequests;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getVerify()
    {
        return $this->verify;
    }

    public function getHeaderAuthorization()
    {
        return $this->headerAuthorization;
    }

    public function getHeaderUserAgent()
    {
        return $this->headerUserAgent;
    }

    public function getHeaderContentType()
    {
        return $this->headerContentType;
    }

    public function getConcurrentRequests()
    {
        return $this->concurrentRequests;
    }

    public function addAdditional($field, $value)
    {
        $this->additionalData[$field] = $value;
        return $this;
    }

    public function getAdditional($field, $default=null)
    {
        if (isset($this->additionalData[$field])) {
            return $this->additionalData[$field];
        }
        return $default;
    }
}