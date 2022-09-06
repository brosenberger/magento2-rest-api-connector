<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\Magento2RestApiConnector\Services
 * @copyright Copyright (c) 2022 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 06.09.2022
 */

namespace BroCode\Magento2RestApiConnector\Services;

use BroCode\Magento2RestApiConnector\Api\Magento2ClientConfiguration;
use BroCode\Magento2RestApiConnector\Factories\Magento2ClientFactory;
use GuzzleHttp\Promise\EachPromise;

/**
 * Service class to execute any Communication with a Magento2 Shop
 */
class RestExecutionService
{
    /**
     * @var Magento2ClientFactory
     */
    protected $clientFactory;

    /**
     * RestExecutionService constructor.
     * @param Magento2ClientFactory $clientFactory
     */
    public function __construct($clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param Magento2ClientConfiguration $configuration
     * @param array $data
     * @param callable $promiseGenerator
     * @param callable|null $rejected
     * @param $concurrentRequests
     * @param callable|null $fulfilled
     * @return void
     */
    public function executeApiUpdate(
        $configuration,
        array $data,
        callable $promiseGenerator,
        callable $rejected = null,
        callable $fulfilled = null
    ) {
        $client = $this->clientFactory->createClient($configuration);
        $promises = (function () use ($data, $client, $promiseGenerator) {
            foreach ($data as $record) {
                yield $promiseGenerator($client, $record);
            }
        })();
        $eachPromise = new EachPromise($promises, [
            'concurrency' => $configuration->getConcurrentRequests(),
            'fulfilled' => $fulfilled,
            'rejected' => $rejected
        ]);
        $eachPromise->promise()->wait();
    }
}