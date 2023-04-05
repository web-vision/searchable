<?php

namespace PAGEmachine\Searchable;

use Elasticsearch\Client;
use PAGEmachine\Searchable\Service\ExtconfService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Manages some index level functions such as clearing the index
 */
class PipelineManager implements SingletonInterface
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
     * @return PipelineManager
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(PipelineManager::class);
    }

    /**
     * Creates all defined pipelines
     */
    public function createPipelines(): void
    {
        foreach (ExtconfService::getInstance()->getPipelines() as $name => $pipelineConfig) {
            $this->createPipeline($name, $pipelineConfig);
        }
    }

    /**
     * (Re-)Creates a pipeline.
     *
     * @param  string $name
     * @param  array  $configuration
     * @return array
     */
    public function createPipeline($name, $configuration)
    {
        return $this->client->ingest()->putPipeline([
            'id' => $name,
            'body' => $configuration,
        ]);
    }
}
