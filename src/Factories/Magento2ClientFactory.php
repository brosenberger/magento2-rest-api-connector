<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\Magento2RestApiConnector\Factories
 * @copyright Copyright (c) 2022 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 06.09.2022
 */

namespace BroCode\Magento2RestApiConnector\Factories;

use BroCode\Magento2RestApiConnector\Api\Magento2ClientConfiguration;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\EachPromise;

/**
 *
 */
class Magento2ClientFactory
{
    /**
     * Creates a Magento2 Client class, depending on the set authorization it might already call Magento
     * to determine the correct authorization parameters for all further communication calls
     * @param Magento2ClientConfiguration $clientConfiguration
     * @return Client
     */
    public function createClient(Magento2ClientConfiguration $clientConfiguration)
    {
        // TODO currently only supports a Bearer Token, add customer and admin user token generation
        return new Client([
            'base_uri' => $clientConfiguration->getBaseUrl(),
            'verify' => $clientConfiguration->getVerify(),
            'headers' => [
                'Authorization' => 'Bearer ' . $clientConfiguration->getHeaderAuthorization(),
                'Content-Type' => $clientConfiguration->getHeaderContentType(),
                'User-Agent' => $clientConfiguration->getHeaderUserAgent()
            ]
        ]);
    }

}