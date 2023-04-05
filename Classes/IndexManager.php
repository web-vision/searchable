<?php

namespace PAGEmachine\Searchable;

use Elasticsearch\Client;
use PAGEmachine\Searchable\Configuration\ConfigurationManager;
use PAGEmachine\Searchable\Service\ConfigurationMergerService;
use PAGEmachine\Searchable\Service\ExtconfService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Manages some index level functions such as clearing the index
 */
class IndexManager implements SingletonInterface
{
    /**
     * Elasticsearch client
     */
    protected Client $client;

    /**
     * @param Client|null $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client ?: Connection::getClient();
    }

    /**
     * @return IndexManager
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(IndexManager::class);
    }

    /**
     * Returns index stats (for backend modules)
     *
     * @return array{health: mixed[], indices: array<int|string, array{name: mixed, language: mixed, types?: array<int|string, array{name: mixed, documents: mixed}>&mixed[]}>}
     */
    public function getStats(): array
    {
        $stats = [];
        $stats['health'] = $this->client->cluster()->health();

        $info = [];

        foreach (ExtconfService::getIndices() as $language => $index) {
            $info[$language] = [
                'name' => $index,
                'language' => $language,
            ];

            foreach (ExtconfService::getIndexers() as $name => $config) {
                $info[$language]['types'][$name] = [
                    'name' => $name,
                    'documents' => $this->client->count([
                        'index' => $index,
                        'type' => $config['config']['type'],
                    ])['count'],
                ];
            }
        }

        $stats['indices'] = $info;

        return $stats;
    }

    /**
     * Deletes and recreates an index
     * @param  string $index
     */
    public function resetIndex($index): void
    {
        $deleteParams = [
            'index' => $index,
        ];

        if ($this->client->indices()->exists($deleteParams)) {
            $this->client->indices()->delete($deleteParams);
        }

        $this->createIndex($index);
    }

    /**
     * Creates an index. Checks if it exists before creating
     * @param  string $index
     * @return array
     */
    public function createIndex($index)
    {
        if ($this->client->indices()->exists(['index' => $index])) {
            return [];
        }

        $params = [
            'index' => $index,
            'body' => [
                'settings' => ConfigurationMergerService::merge(ExtconfService::getDefaultIndexSettings(), ExtconfService::getIndexSettings($index)),
            ],
        ];

        $mapping = ConfigurationManager::getInstance()->getMapping($index);

        if ($mapping !== []) {
            $params['body']['mappings'] = $mapping;
        }

        return $this->client->indices()->create($params);
    }

    /**
     * Resets the update index
     */
    public function resetUpdateIndex(): void
    {
        $this->resetIndex(
            ExtconfService::getInstance()->getUpdateIndex()
        );
    }
}
