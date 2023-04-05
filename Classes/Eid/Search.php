<?php

namespace PAGEmachine\Searchable\Eid;

use PAGEmachine\Searchable\Query\SearchQuery;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Eid-based search class
 */
class Search extends AbstractEidHandler
{
    /**
     * Returns results for given term
     *
     * @param  string $term
     * @return array{results: mixed, totalPages: mixed, currentPage: int} $suggestions
     */
    protected function getResults($term): array
    {
        $query = GeneralUtility::makeInstance(SearchQuery::class);
        $page = (int)($this->options['page'] ?: 1);
        $language = (int)($this->options['lang'] ?: 0);

        $query
            ->setTerm($term)
            ->setPage($page)
            ->setLanguage($language);

        if (isset($this->options['features'])) {
            $query
                ->setPluginMode(true)
                ->setFeatureSettings($this->options['features']);
        }

        $result = $query->execute();

        return [
            'results' => $result,
            'totalPages' => $query->getPageCount(),
            'currentPage' => $page,
        ];
    }
}
