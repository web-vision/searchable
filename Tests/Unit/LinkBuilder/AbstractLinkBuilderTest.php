<?php

namespace PAGEmachine\Searchable\Tests\Unit\LinkBuilder;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Searchable\LinkBuilder\AbstractLinkBuilder;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Testcase for AbstractLinkBuilder
 */
class AbstractLinkBuilderTest extends UnitTestCase
{
    /**
     * @var AbstractLinkBuilder
     */
    protected $linkBuilder;

    protected function setUp(): void
    {
        $this->linkBuilder = $this->getMockForAbstractClass(AbstractLinkBuilder::class);
    }

    /**
     * @test
     * @dataProvider languagesAndLinkConfigurations
     */
    public function createsFixedLinkConfigurationWithLanguage(int $language, array $expectedLinkConfiguration): void
    {
        $record = [];

        $configuration = [
            'titleField' => 'footitle',
            'languageParam' => 'LANG',
            'fixedParts' => [
                'someUid' => 2,
                'additionalParams' => ['foo' => 'bar'],
            ],
            'dynamicParts' => [
            ],
        ];

        $this->linkBuilder = $this->getAccessibleMockForAbstractClass(AbstractLinkBuilder::class, ['config' => $configuration]);
        $linkConfiguration = $this->linkBuilder->createLinkConfiguration($record, $language);

        self::assertEquals($expectedLinkConfiguration, $linkConfiguration);
    }

    public function languagesAndLinkConfigurations(): array
    {
        return [
            'default language' => [
                0,
                [
                    'someUid' => 2,
                    'additionalParams' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            'translation language' => [
                1,
                [
                    'someUid' => 2,
                    'additionalParams' => [
                        'foo' => 'bar',
                        'LANG' => 1,
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function replacesDynamicFields(): void
    {
        $configuration = [
            'languageParam' => 'L',
            'fixedParts' => [],
            'dynamicParts' => [
                'pageUid' => 'page',
            ],
        ];

        $record = [
            'page' => '123',
        ];

        $this->linkBuilder = $this->getAccessibleMockForAbstractClass(AbstractLinkBuilder::class, ['config' => $configuration]);
        $linkConfiguration = $this->linkBuilder->createLinkConfiguration($record, 0);
        $expectedLinkConfiguration = [
            'pageUid' => '123',
        ];

        self::assertEquals($expectedLinkConfiguration, $linkConfiguration);
    }

    /**
     * @test
     */
    public function replacesNestedDynamicFields(): void
    {
        $configuration = [
            'fixedParts' => [],
        ];

        $configuration['dynamicParts'] = [
            'pageUid' => 'page',
            'additionalParams' => [
                'param1' => 'property1',
                'param2' => 'property2',
            ],
        ];

        $record = [
            'page' => '123',
            'property1' => 'value1',
            'property2' => 'value2',
        ];

        $this->linkBuilder = $this->getAccessibleMockForAbstractClass(AbstractLinkBuilder::class, ['config' => $configuration]);
        $linkConfiguration = $this->linkBuilder->createLinkConfiguration($record, 0);

        self::assertSame('123', $linkConfiguration['pageUid'] ?? null);
        self::assertSame(['param1' => 'value1', 'param2' => 'value2'], $linkConfiguration['additionalParams'] ?? null);
    }

    /**
     * @test
     */
    public function unsetsEmptyDynamicFieldsAndUsesFixedPartInstead(): void
    {
        $configuration = [
            'fixedParts' => [],
        ];

        $configuration['fixedParts']['pageUid'] = '123';
        $configuration['dynamicParts']['pageUid'] = 'page';

        $record = [];

        $this->linkBuilder = $this->getAccessibleMockForAbstractClass(AbstractLinkBuilder::class, ['config' => $configuration]);
        $linkConfiguration = $this->linkBuilder->createLinkConfiguration($record, 0);

        self::assertSame('123', $linkConfiguration['pageUid'] ?? null);
    }
}
