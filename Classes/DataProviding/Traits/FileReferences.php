<?php

namespace Sinso\Webcomponents\DataProviding\Traits;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;

trait FileReferences
{
    private FileRepository $fileRepository;

    public function injectFileRepository(FileRepository $fileRepository): void
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @return FileReference[]
     */
    protected function loadFileReferences(array $record, string $fieldName, string $localTableName = 'tt_content'): array
    {
        $localUid = $record['_LOCALIZED_UID'] ?? $record['uid'] ?? null;
        if (empty($localUid)) {
            return [];
        }
        return $this->fileRepository->findByRelation($localTableName, $fieldName, $localUid);
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
