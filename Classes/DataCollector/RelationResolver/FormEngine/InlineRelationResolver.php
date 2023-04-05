<?php

namespace PAGEmachine\Searchable\DataCollector\RelationResolver\FormEngine;

use PAGEmachine\Searchable\DataCollector\DataCollectorInterface;
use PAGEmachine\Searchable\DataCollector\RelationResolver\RelationResolverInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

class InlineRelationResolver implements SingletonInterface, RelationResolverInterface
{
    /**
     * @return SelectRelationResolver
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * Resolves a select relation. Placeholder relation resolver until RelationResolvers are integrated into FormEngine DataProviders
     *
     * @param  string $fieldname
     * @param  array $record The record containing the field to resolve
     * @return array<int, mixed[]> $processedField
     */
    public function resolveRelation($fieldname, $record, DataCollectorInterface $childCollector, DataCollectorInterface $parentCollector): array
    {
        $records = [];

        if (!empty($record[$fieldname])) {
            foreach ($record[$fieldname] as $rawChild) {
                // This childCollector call is actually redundant, as the FormEngine already delivers the whole child record.
                // However, it needs to be called to filter out fields and call further subCollectors.
                //@TODO: Restructure DataCollectors and RelationResolvers to improve this
                $records[] = $childCollector->getRecord($rawChild['uid']);
            }
        }
        return $records;
    }
}
