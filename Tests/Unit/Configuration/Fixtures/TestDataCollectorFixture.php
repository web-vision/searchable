<?php

namespace PAGEmachine\Searchable\Tests\Unit\Configuration\Fixtures;

use PAGEmachine\Searchable\DataCollector\AbstractDataCollector;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

class TestDataCollectorFixture extends AbstractDataCollector
{
    protected static $defaultConfiguration = [
        'option1' => 1,
        'option2' => 2,
    ];

    /**
     * Fetches a record
     *
     * @param  int $identifier
     */
    public function getRecord($identifier): array
    {
        return [];
    }

    public function getRecords(): array
    {
        return [];
    }
}
