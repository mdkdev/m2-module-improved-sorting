<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_ImprovedSorting
 */

declare(strict_types=1);

namespace Mdkdev\ImprovedSorting\Model\Client;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Exception;
use Magento\Elasticsearch\Model\Adapter\Index\IndexNameResolver;
use Magento\Elasticsearch\Model\Config as ElasticsearchConfig;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Mdkdev\ImprovedSorting\Model\Collector\CollectorInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ElasticSearch
 * @package Mdkdev\ImprovedSorting\Model\Client
 */
class ElasticSearch
{
    private ?Client $client = null;
    private array $elasticClient = [];

    /**
     * @param ElasticsearchConfig $elasticConfig
     * @param IndexNameResolver $indexNameResolver
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     * @param array $clientOptions
     * @throws LocalizedException
     */
    public function __construct(
        private readonly ElasticsearchConfig $elasticConfig,
        private readonly IndexNameResolver $indexNameResolver,
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
        private array $clientOptions
    ) {
        $this->clientOptions = $this->elasticConfig->prepareClientOptions($this->clientOptions);

        try {
            $this->client = $this->getElasticSearchClient();
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());

            throw new LocalizedException(
                __('The search failed because of a search engine misconfiguration.')
            );
        }
    }

    /**
     * @return Client|null
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function exists(array $data): bool
    {
        return $this->getClient()->exists($data);
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getESData(
        int $productId,
        int $storeId
    ): array {
        return [
            'id' => $productId,
            'index' => $this->getIndexNameByStoreId($storeId)
        ];
    }

    /**
     * @param array $eSData
     * @param array $productData
     * @param bool $hasChanges
     * @return void
     */
    public function update(
        array $eSData,
        array $productData,
        bool &$hasChanges
    ): void {
        $sourceData = $this->getClient()->getSource($eSData);

        foreach ($productData as $attribute => $value) {
            if ($attribute === CollectorInterface::PRODUCT_ID) {
                continue;
            }

            if (!isset($sourceData[$attribute]) || $sourceData[$attribute] !== $value) {
                $hasChanges = true;
                $eSData['body'] = $this->getBody($productData);

                $this->client->update($eSData);

                break;
            }
        }
    }

    /**
     * @param array $productData
     * @return string
     */
    private function getBody(array $productData): string
    {
        unset($productData[CollectorInterface::PRODUCT_ID]);

        return $this->serializer->serialize([
            'doc' => $productData
        ]);
    }

    /**
     * @param int $storeId
     * @return string
     */
    private function getIndexNameByStoreId(int $storeId): string
    {
        return $this->indexNameResolver->getIndexName(
            $storeId,
            ElasticsearchConfig::ELASTICSEARCH_TYPE_DEFAULT,
            []
        );
    }

    /**
     * @return Client
     */
    private function getElasticSearchClient(): Client
    {
        $pid = \getmypid();

        if (!isset($this->elasticClient[$pid])) {
            $config = $this->buildESConfig($this->clientOptions);
            $this->elasticClient[$pid] = ClientBuilder::fromConfig($config, true);
        }

        return $this->elasticClient[$pid];
    }

    /**
     * @param array $options
     * @return array
     */
    private function buildESConfig(array $options = []): array
    {
        $authString = $portString = '';
        $hostname = \preg_replace('/http[s]?:\/\//i', '', $options['hostname']);
        // @codingStandardsIgnoreStart
        $protocol = \parse_url($options['hostname'], PHP_URL_SCHEME);
        // @codingStandardsIgnoreEnd
        if (!$protocol) {
            $protocol = 'http';
        }

        if (!empty($options['enableAuth']) && (int)$options['enableAuth'] === 1) {
            $authString = "{$options['username']}:{$options['password']}@";
        }

        if (!empty($options['port'])) {
            $portString = ':' . $options['port'];
        }

        $host = $protocol . '://' . $authString . $hostname . $portString;

        $options['hosts'] = [$host];

        return $options;
    }
}
