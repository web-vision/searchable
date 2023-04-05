<?php

namespace PAGEmachine\Searchable\Tests\Unit\DataCollector\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Searchable\DataCollector\Utility\FieldListUtility;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Testcase for FieldListUtility
 */
class FieldListUtilityTest extends UnitTestCase
{
    /**
     * @var FieldListUtility
     */
    protected $fieldListUtility;

    /**
     * Set up this testcase
     */
    protected function setUp(): void
    {
        $this->fieldListUtility = new FieldListUtility();
    }

    /**
     * @test
     * @dataProvider whitelistCombinations
     */
    public function evaluatesWhitelistItem(string $item, array $list, bool $allowed): void
    {
        self::assertEquals($allowed, $this->fieldListUtility->shouldInclude($item, $list, FieldListUtility::MODE_WHITELIST));
    }

    public function whitelistCombinations(): array
    {
        return [
            'item included' => ['allowed', ['allowed', 'foo', 'bar'], true],
            'item not included' => ['notallowed', ['allowed', 'foo', 'bar'], false],
            'empty whitelist' => ['notallowed', [], false],
        ];
    }

    /**
     * @test
     * @dataProvider blacklistCombinations
     */
    public function evaluatesBlacklistItem(string $item, array $list, bool $allowed): void
    {
        self::assertEquals($allowed, $this->fieldListUtility->shouldInclude($item, $list, FieldListUtility::MODE_BLACKLIST));
    }

    public function blacklistCombinations(): array
    {
        return [
            'item included' => ['notallowed', ['notallowed', 'foo', 'bar'], false],
            'item not included' => ['allowed', ['notallowed', 'foo', 'bar'], true],
            'empty blacklist' => ['allowed', [], true],
        ];
    }
}
