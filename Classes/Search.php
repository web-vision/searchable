<?php
namespace PAGEmachine\Searchable;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use PAGEmachine\Searchable\Service\ExtconfService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * The main class for searching
 */
class Search implements SingletonInterface {

    /**
     * Elasticsearch client
     * @var Client
     */
    protected $client;

    /**
     * @param Client|null $client
     */
    public function __construct(Client $client = null) {

        $this->client = $client ?: ClientBuilder::create()->build();
    }

    /**
     * @return Search
     */
    public static function getInstance() {

        return GeneralUtility::makeInstance(Search::class);

    }

    /**
     * Search everything (all indices and types) for the term
     * @param  string $term
     * @param  boolean $respectLanguage If set, the search will be limited to the current FE language (if there is an index for it) or the default language
     * @param  int $forceLanguage Forces the given language id
     * @return array
     */
    public function search($term, $respectLanguage = true, $forceLanguage = null) {

        $params = [
            'body' => [
                'query' => [
                    'match' => [
                        '_all' => $term
                    ]
                ]
            ]
        ];

        if ($respectLanguage === true) {

            $language = $forceLanguage ?: $GLOBALS['TSFE']->sys_language_uid;

            $params['index'] = ExtconfService::hasIndex($language) ? ExtconfService::getIndex($language) : ExtconfService::getIndex();
        }
        

        $result = $this->client->search($params);
        return $result;
    }




}
