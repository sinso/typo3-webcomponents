<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;

class FileReferencesHelper
{
    public function __construct(
        private readonly FileRepository $fileRepository,
    ) {}

    /**
     * @param array<string, string|int> $record
     * @return array<FileReference>
     */
    public function loadFileReferences(array $record, string $fieldName, string $localTableName = 'tt_content'): array
    {
        $localUid = $record['_LOCALIZED_UID'] ?? $record['uid'] ?? null;
        if (empty($localUid)) {
            return [];
        }
        return $this->fileRepository->findByRelation($localTableName, $fieldName, (int)$localUid);
    }

    /**
     * @param array<string, string|int> $record
     */
    public function loadFileReference(array $record, string $fieldName, string $localTableName = 'tt_content'): ?FileReference
    {
        $fileReferences = $this->loadFileReferences($record, $fieldName, $localTableName);
        if (empty($fileReferences)) {
            return null;
        }
        return $fileReferences[0];
    }
}
