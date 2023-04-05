<?php

namespace PAGEmachine\Searchable\Tests\Unit\Feature;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Searchable\Feature\HighlightFeature;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Testcase for HighlightFeature
 */
class HighlightFeatureTest extends UnitTestCase
{
    /**
     * @var HighlightFeature
     */
    protected $feature;

    /**
     * Set up this testcase
     */
    public function setUp(): void
    {
        $this->feature = new HighlightFeature();
    }

    /**
     * @test
     */
    public function addsFieldMapping(): void
    {
        $configuration = [
            'fields' => [
                'fieldone',
            ],
            'highlightField' => 'searchable_highlight',
        ];

        $mapping = HighlightFeature::modifyMapping([], $configuration);

        self::assertEquals('searchable_highlight', $mapping['properties']['fieldone']['copy_to'] ?? null);
    }

    /**
     * @test
     */
    public function keepsExistingMappingPropiertes(): void
    {
        $configuration = [
            'fields' => [
                'fieldone',
            ],
            'highlightField' => 'searchable_highlight',
        ];

        $mapping = [
            'properties' => [
                'fieldone' => [
                    'type' => 'text',
                ],
            ],
        ];

        $mapping = HighlightFeature::modifyMapping($mapping, $configuration);

        self::assertEquals('text', $mapping['properties']['fieldone']['type'] ?? null);
        self::assertEquals('searchable_highlight', $mapping['properties']['fieldone']['copy_to'] ?? null);
    }

    /**
     * @test
     */
    public function mapsRecursively(): void
    {
        $configuration = [
            'fields' => [
                'fieldone',
                'sublevel' => [
                    'fieldtwo',
                ],
            ],
            'highlightField' => 'searchable_highlight',
        ];

        $mapping = [
            'properties' => [
                'fieldone' => [
                    'type' => 'text',
                ],
                'sublevel' => [
                    'properties' => [
                        'fieldtwo' => [
                            'type' => 'text',
                        ],
                    ],
                ],
            ],
        ];

        $mapping = HighlightFeature::modifyMapping($mapping, $configuration);

        self::assertEquals('text', $mapping['properties']['fieldone']['type'] ?? null);
        self::assertEquals('searchable_highlight', $mapping['properties']['fieldone']['copy_to'] ?? null);

        self::assertEquals('text', $mapping['properties']['sublevel']['properties']['fieldtwo']['type'] ?? null);
        self::assertEquals('searchable_highlight', $mapping['properties']['sublevel']['properties']['fieldtwo']['copy_to'] ?? null);
    }
}
