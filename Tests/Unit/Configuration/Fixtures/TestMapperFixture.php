<?php

namespace PAGEmachine\Searchable\Tests\Unit\Configuration\Fixtures;

use PAGEmachine\Searchable\Mapper\DefaultMapper;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

class TestMapperFixture extends DefaultMapper
{
    /**
     * Creates the mapping
     *
     * @param  array $indexerConfiguration The toplevel configuration for one indexer
     */
    public static function getMapping($indexerConfiguration): array
    {
        return ['mapperValue', 'newMapperValue'];
    }
}
