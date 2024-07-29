<?php

namespace Sinso\Webcomponents\DataProviding\Traits;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait FileReferences
{
    /**
     * @return FileReference[]
     */
    protected function loadFileReferences(array $record, string $fieldName, string $localTableName = 'tt_content'): array
    {
        $localUid = $record['_LOCALIZED_UID'] ?? $record['uid'] ?? null;
        if (empty($localUid)) {
            return [];
        }
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        return $fileRepository->findByRelation($localTableName, $fieldName, $localUid);
    }

    protected function loadFileReference(array $record, string $fieldName, string $localTableName = 'tt_content'): ?FileReference
    {
        $fileReferences = $this->loadFileReferences($record, $fieldName, $localTableName);
        if (empty($fileReferences)) {
            return null;
        }
        return $fileReferences[0];
    }
}
