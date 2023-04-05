<?php

namespace PAGEmachine\Searchable\Utility;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Helper class for all extconf related settings
 */
class BinaryConversionUtility
{
    /**
     * Converts binary ckeckbox values into an array containing all active keys
     *
     * @param  int $value the raw checkbox value
     * @param  int $itemCount max amount of items in this checkbox
     * @return int[]
     */
    public static function convertCheckboxValue($value, $itemCount = 31): array
    {
        $checkedItemKeys = [];

        for ($i=0; $i < $itemCount; $i++) {
            $pow = 2 ** $i;
            if (($value & $pow) !== 0) {
                $checkedItemKeys[] = $i;
            }
        }

        return $checkedItemKeys;
    }
}
