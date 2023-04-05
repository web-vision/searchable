<?php

namespace PAGEmachine\Searchable\Query;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Helper class to build up the parameter array for bulk indexing
 */
class BulkQuery extends AbstractQuery
{
    /**
     * @var string $index
     */
    protected $index;

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param string $index
     */
    public function setIndex($index): void
    {
        $this->index = $index;
    }

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @var string $pipeline
     */
    protected $pipeline;

    /**
     * @return string
     */
    public function getPipeline()
    {
        return $this->pipeline;
    }

    /**
     * @param string $pipeline
     */
    public function setPipeline($pipeline): void
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @param string $index
     * @param string $type
     */
    public function __construct($index, $type, $pipeline = null)
    {
        parent::__construct();

        $this->index = $index;
        $this->type = $type;
        $this->pipeline = $pipeline;

        $this->init();
    }

    /**
     * Creates the basic information for bulk indexing
     */
    public function init(): void
    {
        $this->parameters =  [
            'index' => $this->getIndex(),
            'type' => $this->getType(),
            'body' => [],
        ];

        if ($this->getPipeline() != null) {
            $this->parameters['pipeline'] = $this->getPipeline();
        }
    }

    /**
     * Adds a new row to the indexer parameters
     *
     * @param int $uid The uid of the current record
     * @param array $body
     */
    public function addRow($uid, $body): void
    {
        //Build meta row for new row
        $this->parameters['body'][] = [
            'index' => [
                '_index' => $this->index,
                '_type' => $this->type,
                '_id' => $uid,
            ],

        ];

        $this->parameters['body'][] = $body;
    }

    public function addRows($uidField, $records): void
    {
        foreach ($records as $record) {
            $this->addRow($record[$uidField], $record);
        }
    }

    /**
     * Executes a bulk insertion query
     *
     * @return array
     */
    public function execute()
    {
        $response = [];

        if (!empty($this->parameters['body'])) {
            $response = $this->client->bulk($this->getParameters());

            if ($response['errors']) {
                $this->logger->error('Bulk Query response contains errors: ', $response);
            }
        }

        return $response;
    }

    /**
     * Deletes an existing document
     * @todo move this away from the bulkquery (does not fit its domain)
     *
     * @param  int $id
     */
    public function delete($id): void
    {
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
        ];

        if ($this->client->exists($params)) {
            $response = $this->client->delete($params);

            if ($response['errors']) {
                $this->logger->error('Delete Query response contains errors: ', $response);
            }
        }
    }

    /**
     * Resets the body (for batch indexing)
     */
    public function resetBody(): void
    {
        $this->parameters['body'] = [];
    }
}
