<?php

namespace PAGEmachine\Searchable\Utility;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Helper class to create a valid TypoScriptFrontendController on demand
 */
class TsfeUtility
{
    /**
     * Initializes TSFE. This is necessary to have proper environment for typoLink.
     */
    public static function createTSFE()
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $site = array_values($siteFinder->getAllSites())[0] ?? null;

        if (!$site instanceof Site) {
            throw new \RuntimeException('No site found for TSFE setup', 1610444900);
        }

        $requestFactory = GeneralUtility::makeInstance(ServerRequestFactory::class);
        $request = $requestFactory->createServerRequest('get', 'http://localhost')
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('site', $site)
            ->withAttribute('language', $site->getDefaultLanguage())
            ->withAttribute('routing', new PageArguments($site->getRootPageId(), '0', []))
            ->withAttribute('frontend.user', GeneralUtility::makeInstance(FrontendUserAuthentication::class));
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $frontendController = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            GeneralUtility::makeInstance(Context::class),
            $request->getAttribute('site'),
            $request->getAttribute('language'),
            $request->getAttribute('routing'),
            $request->getAttribute('frontend.user')
        );
        $frontendController->determineId($request);
        $frontendController->getConfigArray($request);

        $GLOBALS['TSFE'] = $frontendController;
    }
}
