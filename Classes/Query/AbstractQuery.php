<?php
namespace PAGEmachine\Searchable\Query;

use Elasticsearch\Client;
use PAGEmachine\Searchable\Configuration\ConfigurationManager;
use PAGEmachine\Searchable\Connection;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Abstract helper class for elasticsearch querying
 */
abstract class AbstractQuery {

    /**
     * The array that is filled and later sent to the elasticsearch client for bulk indexing
     * 
     * @var array $parameters
     */
    protected $parameters = [];
    
    /**
     * @return array
     */
    public function getParameters() {
      return $this->parameters;
    }
    
    /**
     * @param array $parameters
     * @return void
     */
    public function setParameters($parameters) {
      $this->parameters = $parameters;
    }
    
    /**
     * @param string $key
     * @return mixed
     */
    public function getParameter($key)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : null;
    }
    
    /**
     * @param string $key
     * @param mixed $parameter
     * @return void
     */
    public function setParameter($key, $parameter)
    {
        $this->parameters[$key] = $parameter;
    }

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Features
     *
     * @var array
     */
    protected $features = [];

    /**
     * @var array $featureSettings
     */
    protected $featureSettings;
    
    /**
     * @return array
     */
    public function getFeatureSettings()
    {
        return $this->featureSettings;
    }
    
    /**
     * @param array $featureSettings
     * @return void
     */
    public function setFeatureSettings($featureSettings)
    {
        $this->featureSettings = $featureSettings;
        return $this;
    }

    /**
     * @param Client|null $client
     * @param Logger|null $logger
     * @param array $features
     */
    public function __construct(Client $client = null, Logger $logger = null, $features = null) {

        $this->client = $client ?: Connection::getClient();
        $this->logger = $logger ?: GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)->getLogger(__CLASS__);

        // Use get_class() instead of static self::class to retrieve the inherited child classname
        $features = $features ?: ConfigurationManager::getInstance()->getQueryConfiguration(get_class($this))['features'];

        if (!empty($features)) {

            foreach ($features as $key => $feature) {
                $this->features[$key] = GeneralUtility::makeInstance($feature['className'], $feature['config']);

            }
        }
    }

    /**
     * Execute method, should be overriden with the concrete command to the client
     * and return the response
     * 
     * @return array
     */
    public function execute() {

        return [];
    }

    /**
     * Apply features to query
     *
     */
    protected function applyFeatures()
    {
        foreach ($this->features as $name => $feature) {

            if ($this->isFeatureEnabled($name)) {

                $this->parameters = $feature->modifyQuery($this->parameters);
            }
        }
    }

    /**
     * Checks if a feature is enabled for this query
     *
     * @param string  $featureName
     * @return boolean
     */
    public function isFeatureEnabled($featureName) {

        if (
            isset($this->featureSettings[$featureName])
            && $this->featureSettings[$featureName] == 1
            ) {

            return true;
        }
        return false;
    }

}
