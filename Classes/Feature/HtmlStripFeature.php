<?php

namespace PAGEmachine\Searchable\Feature;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Feature to strip HTML from all record fields
 */
class HtmlStripFeature extends AbstractFeature implements FeatureInterface
{
    /**
     * @var string
     */
    public static $featureName = 'htmlStrip';

    /**
     * Strip HTML from all record fields
     *
     * @param  array  $record
     * @return array  $record
     */
    public function modifyRecord($record)
    {
        array_walk_recursive($record, function (&$value): void {
            if (is_string($value)) {
                $value = strip_tags($value);
            }
        });

        return $record;
    }
}
