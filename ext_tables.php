<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (version_compare(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version(), '11', '>=')) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Searchable',
        'web',
        'searchable',
        '',
        [
            \PAGEmachine\Searchable\Controller\BackendController::class => 'start, search, request, resetIndices, indexFull, indexPartial',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:searchable/ext_icon.svg',
            'labels' => 'LLL:EXT:searchable/Resources/Private/Language/locallang_mod.xlf',
        ]
    );
} else {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'PAGEmachine.Searchable',
        'web',
        'searchable',
        '',
        [
            'Backend' => 'start, search, request, resetIndices, indexFull, indexPartial',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:searchable/ext_icon.svg',
            'labels' => 'LLL:EXT:searchable/Resources/Private/Language/locallang_mod.xlf',
        ]
    );
}
