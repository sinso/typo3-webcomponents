<?php

namespace Sinso\Webcomponents\DataProvider\Traits;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait FileReferences
{
    /**
     * @param string $fieldName
     * @param ?int $localUid
     * @param string $localTableName
     * @return FileReference[]
     */
    protected function loadFileReferences(string $fieldName, ?int $localUid, string $localTableName = 'tt_content'): array
    {
        if (empty($localUid)) {
            return [];
        }
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        return $fileRepository->findByRelation($localTableName, $fieldName, $localUid);
    }

    protected function loadFileReference(string $fieldName, ?int $localUid, string $localTableName = 'tt_content'): ?FileReference
    {
        $fileReferences = $this->loadFileReferences($fieldName, $localUid, $localTableName);
        if (empty($fileReferences)) {
            return null;
        }
        return $fileReferences[0];
    }
}
