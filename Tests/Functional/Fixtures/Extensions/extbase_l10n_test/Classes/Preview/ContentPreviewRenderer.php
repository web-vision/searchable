<?php

declare(strict_types = 1);

namespace Pagemachine\SearchableExtbaseL10nTest\Preview;

use PAGEmachine\Searchable\Preview\PreviewRendererInterface;
use Pagemachine\SearchableExtbaseL10nTest\Domain\Repository\ContentRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

final class ContentPreviewRenderer implements PreviewRendererInterface
{
    /**
     * @param  array $record
     */
    public function render($record): string
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $repository = $objectManager->get(ContentRepository::class);
        $content = $repository->findByIdentifier($record['uid']);

        return sprintf(
            'Preview: %s [%d]',
            $content->getHeader(),
            $content->_getProperty('_localizedUid')
        );
    }
}
