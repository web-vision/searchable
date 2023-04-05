<?php

namespace PAGEmachine\Searchable\Query;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

use PAGEmachine\Searchable\Configuration\ConfigurationManager;

/**
 * Query for partial index updates of database record changes
 */
class DatabaseRecordUpdateQuery
{
    protected UpdateQuery $updateQuery;

    protected array $updateConfiguration;

    /**
     * @param array|null $updateConfiguration
     */
    public function __construct(UpdateQuery $updateQuery = null, array $updateConfiguration = null)
    {
        $this->updateQuery = $updateQuery ?: new UpdateQuery();
        $this->updateConfiguration = $updateConfiguration ?: ConfigurationManager::getInstance()->getUpdateConfiguration();
    }

    /**
     * Register a toplevel update
     *
     * @param string $table
     * @param int $uid
     */
    public function updateToplevel($table, $uid): void
    {
        $configuration = empty($this->updateConfiguration['database']['toplevel'][$table]) ? [] : $this->updateConfiguration['database']['toplevel'][$table];
        foreach ($configuration as $type) {
            $this->updateQuery->addUpdate($type, 'uid', (int)$uid);
        }
    }

    /**
     * Register a sublevel update
     *
     * @param string $table
     * @param int $uid
     */
    public function updateSublevel($table, $uid): void
    {
        $configuration = empty($this->updateConfiguration['database']['sublevel'][$table]) ? [] : $this->updateConfiguration['database']['sublevel'][$table];
        foreach ($configuration as $type => $path) {
            $this->updateQuery->addUpdate($type, $path . '.uid', (int)$uid);
        }
    }
}
