<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\Magento2RestApiConnector\Services
 * @copyright Copyright (c) 2022 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 06.09.2022
 */

namespace BroCode\Magento2RestApiConnector\Services;

use BroCode\Magento2RestApiConnector\Api\Magento2ClientConfiguration;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;

/**
 *
 */
class ProductRestService
{
    /**
     * @var RestExecutionService
     */
    private $restService;

    /**
     * ProductRestService constructor.
     * @param RestExecutionService $restService
     */
    public function __construct(RestExecutionService $restService)
    {
        $this->restService = $restService;
    }

    /**
     * @param Magento2ClientConfiguration $configuration
     * @param array $searchParam
     * @param int $pageSize
     * @param string $fields
     * @param callable|null $rejected
     * @param callable|null $fulfilled
     * @return void
     */
    public function searchProductData(
        $configuration,
        $searchParam,
        $pageSize = 10,
        $currentPage = 1,
        $fields = '',
        callable $rejected = null,
        callable $fulfilled = null
    ) {
        $this->restService->executeApiUpdate(
            $configuration,
            [['q' => $searchParam, 'pageSize'=> $pageSize, 'currentPage' => $currentPage, 'fields' => $fields]],
            function($client, $record) {
                $promise = new Promise();
                $query = [
                    'searchCriteria[page_size]' => $record['pageSize'],
                    'searchCriteria[current_page]' => $record['currentPage'],
                ];
                if (!empty($record['q'])) {
                    $filterGroupCount = 0;
                    $count = 0;
                    foreach ($record['q'] as $filterGroup) {
                        foreach ($filterGroup as $criteria)  {
                            list($field,  $conditionType, $value) = $criteria;
                            $query['searchCriteria[filter_groups][' . $filterGroupCount .'][filters]['.$count.'][field]']=$field;
                            $query['searchCriteria[filter_groups][' . $filterGroupCount .'][filters]['.$count.'][value]']=$value;
                            $query['searchCriteria[filter_groups][' . $filterGroupCount .'][filters]['.$count.'][condition_type]']=$conditionType;
                            $count++;
                        }
                        $filterGroupCount++;
                    }
                }
                if (!empty($record['fields'])) {
                    $query['fields'] = $record['fields'];
                }
                /** @var Client $client */
                $promise->resolve($client->get('rest/all/V1/products', [
                    'query' => $query
                ]));

                return $promise;
            },
            $rejected,
            $fulfilled
        );
    }

    /**
     * @param Magento2ClientConfiguration $configuration
     * @return void
     */
    public function updateProductData(
        $configuration,
        $productData,
        callable $rejected = null,
        callable $fulfilled = null
    ) {
        $this->restService->executeApiUpdate(
            $configuration,
            $productData,
            function ($client, $record) {
                /** @var Client $client */
                return $client->putAsync(
                    'rest/all/V1/products/' . $record['sku'],
                    [
                        'body' => json_encode([
                            'product' => $record
                        ])
                    ]
                )->then(function (Response $response) {
                    return $response->getBody()->getContents();
                });
            },
            $rejected,
            $fulfilled
        );
    }

    public function generateCustomAttributesData(array $data)
    {
        return array_map(function ($attributeCode, $value) {
            return [
                'attribute_code' => $attributeCode,
                'value' => $value
            ];
        }, array_keys($data), $data);
    }
}
