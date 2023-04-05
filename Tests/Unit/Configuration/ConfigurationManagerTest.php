<?php

namespace PAGEmachine\Searchable\Tests\Unit\Configuration;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Searchable\Configuration\ConfigurationManager;
use PAGEmachine\Searchable\Service\ExtconfService;
use PAGEmachine\Searchable\Tests\Unit\Configuration\Fixtures\TestDataCollectorFixture;
use PAGEmachine\Searchable\Tests\Unit\Configuration\Fixtures\TestFeatureFixture;
use PAGEmachine\Searchable\Tests\Unit\Configuration\Fixtures\TestIndexerFixture;
use PAGEmachine\Searchable\Tests\Unit\Configuration\Fixtures\TestMapperFixture;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Testcase for ConfigurationManager
 */
class ConfigurationManagerTest extends UnitTestCase
{
    use ProphecyTrait;

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var ExtonfService
     */
    protected $extconfService;

    /**
     * Set up this testcase
     */
    protected function setUp(): void
    {
        $this->configurationManager = new ConfigurationManager();

        $this->extconfService = $this->prophesize(ExtconfService::class);

        GeneralUtility::setSingletonInstance(ExtconfService::class, $this->extconfService->reveal());
    }

    /**
     * @test
     */
    public function mergesToplevelConfiguration(): void
    {
        $configuration = [
            'pages' => [
                'className' => TestIndexerFixture::class,
                'config' => [
                    'type' => 'pages',
                ],
            ],
        ];
        $this->extconfService->getIndexerConfiguration()->willReturn($configuration);

        $expectedConfiguration = [
            'pages' => [
                'className' => TestIndexerFixture::class,
                'config' => [
                    'type' => 'pages',
                    'customOption' => 1,
                ],
            ],
        ];

        self::assertEquals($expectedConfiguration, $this->configurationManager->getIndexerConfiguration());
    }

    /**
     * @test
     */
    public function doesNothingIfNoClassIsAvailable(): void
    {
        $configuration = [
            'pages' => [
                'config' => [
                    'type' => 'pages',
                ],
            ],
        ];
        $this->extconfService->getIndexerConfiguration()->willReturn($configuration);

        self::assertEquals($configuration, $this->configurationManager->getIndexerConfiguration());
    }

    /**
     * @test
     */
    public function mergesRecursiveConfiguration(): void
    {
        $configuration = [
            'pages' => [
                'className' => TestIndexerFixture::class,
                'config' => [
                    'type' => 'pages',
                    'collector' => [
                        'className' => TestDataCollectorFixture::class,
                        'config' => [
                            'extconfOption' => 'foobar',
                        ],
                    ],
                ],
            ],
        ];
        $this->extconfService->getIndexerConfiguration()->willReturn($configuration);

        $expectedConfiguration = [
            'pages' => [
                'className' => TestIndexerFixture::class,
                'config' => [
                    'type' => 'pages',
                    'customOption' => 1,
                    'collector' => [
                        'className' => TestDataCollectorFixture::class,
                        'config' => [
                            'extconfOption' => 'foobar',
                            'option1' => 1,
                            'option2' => 2,
                        ],
                    ],
                ],
            ],
        ];

        self::assertEquals($expectedConfiguration, $this->configurationManager->getIndexerConfiguration());
    }

    /**
     * @test
     */
    public function mergesMultipleConfigurationsOnTheSameLevel(): void
    {
        $configuration = [
            'pages' => [
                'className' => TestIndexerFixture::class,
                'config' => [
                    'type' => 'pages',
                    'collector' => [
                        'className' => TestDataCollectorFixture::class,
                        'config' => [
                            'extconfOption' => 'foobar',
                            'subCollectors' => [
                                'myType' => [
                                    'className' => TestDataCollectorFixture::class,
                                    'config' => [
                                        'subExtconfOption' => 'barbaz',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->extconfService->getIndexerConfiguration()->willReturn($configuration);

        $expectedConfiguration = [
            'pages' => [
                'className' => TestIndexerFixture::class,
                'config' => [
                    'type' => 'pages',
                    'customOption' => 1,
                    'collector' => [
                        'className' => TestDataCollectorFixture::class,
                        'config' => [
                            'extconfOption' => 'foobar',
                            'option1' => 1,
                            'option2' => 2,
                            'subCollectors' => [
                                'myType' => [
                                    'className' => TestDataCollectorFixture::class,
                                    'config' => [
                                        'subExtconfOption' => 'barbaz',
                                        'option1' => 1,
                                        'option2' => 2,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        self::assertEquals($expectedConfiguration, $this->configurationManager->getIndexerConfiguration());
    }

    /**
     * @test
     */
    public function createsMappingWithUserPrecedence(): void
    {
        $configuration = [
            'pages' => [
                'className' => TestIndexerFixture::class,
                'config' => [
                    'type' => 'pages',
                    'mapper' => [
                        'className' => TestMapperFixture::class,
                    ],
                    'mapping' => [
                        'properties' => [
                            'existingKey' => 'existingValue',
                            'overrideKey' => 'overrideValue',
                        ],
                    ],
                ],
            ],
        ];
        $this->extconfService->getIndexerConfiguration()->willReturn($configuration);

        $mapping = $this->configurationManager->getMapping('pages');

        self::assertEquals('existingValue', $mapping['pages']['properties']['existingKey']);
        self::assertEquals('overrideValue', $mapping['pages']['properties']['overrideKey']);
        self::assertEquals('newMapperValue', $mapping['pages']['properties']['newKey']);
    }

    /**
     * @test
     */
    public function enrichesMappingByFeatures(): void
    {
        $configuration = [
            'pages' => [
                'className' => TestIndexerFixture::class,
                'config' => [
                    'type' => 'pages',
                    'features' => [
                        0 => [
                            'className' => TestFeatureFixture::class,
                        ],

                    ],
                    'mapping' => [
                        'properties' => [
                            'existingKey' => 'existingValue',
                            'overrideKey' => 'overrideValue',
                        ],
                    ],
                ],
            ],
        ];
        $this->extconfService->getIndexerConfiguration()->willReturn($configuration);

        $mapping = $this->configurationManager->getMapping('pages');

        self::assertEquals('existingValue', $mapping['pages']['properties']['existingKey']);
        self::assertEquals('overrideValue', $mapping['pages']['properties']['overrideKey']);
        self::assertEquals('featurevalue', $mapping['pages']['featureproperty']);
    }

    /**
     * @test
     */
    public function createsUpdateConfiguration(): void
    {
        $configuration = [
            'pages' => [
                'className' => TestIndexerFixture::class,
                'config' => [
                    'type' => 'pages',
                    'collector' => [
                        'className' => TestDataCollectorFixture::class,
                        'config' => [
                            'table' => 'pagestable',
                            'subCollectors' => [
                                'myType' => [
                                    'className' => TestDataCollectorFixture::class,
                                    'config' => [
                                        'table' => 'contenttable',
                                        'field' => 'content',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'extensioncontent' => [
                'className' => TestIndexerFixture::class,
                'config' => [
                    'type' => 'extensiontype',
                    'collector' => [
                        'className' => TestDataCollectorFixture::class,
                        'config' => [
                            'table' => 'extensiontable',
                            'subCollectors' => [
                                'myType' => [
                                    'className' => TestDataCollectorFixture::class,
                                    'config' => [
                                        'table' => 'contenttable',
                                        'field' => 'content',
                                    ],
                                ],
                                'myType2' => [
                                    'className' => TestDataCollectorFixture::class,
                                    'config' => [
                                        'table' => 'othertable',
                                        'field' => 'othercontent',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->extconfService->getIndexerConfiguration()->willReturn($configuration);

        $expectedUpdateConfiguration = [
            'database' => [
                'toplevel' => [
                    'pagestable' => ['pages'],
                    'extensiontable' => ['extensiontype'],
                ],
                'sublevel' => [
                    'contenttable' => [
                        'pages' => 'content',
                        'extensiontype' => 'content',
                    ],
                    'othertable' => [
                        'extensiontype' => 'othercontent',
                    ],
                ],
            ],
        ];

        self::assertEquals($expectedUpdateConfiguration, $this->configurationManager->getUpdateConfiguration());
    }
}
