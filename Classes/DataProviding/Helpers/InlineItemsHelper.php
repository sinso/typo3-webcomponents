<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use Doctrine\DBAL\ArrayParameterType;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class reliably loads inline items for a given parent record.
 * It considers workspaces and localization and is galvanized with many functional tests.
 */
class InlineItemsHelper
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly Context $context,
        private readonly PageRepository $pageRepository,
    ) {}

    /**
     * @param array<string, int|string> $parentRecord
     * @return list<array<string, mixed>>
     */
    public function loadInlineItems(array $parentRecord, string $inlineFieldName, string $parentTable = 'tt_content'): array
    {
        // if the field is empty, and we're not in a workspace context, then there are no inline items
        $workspaceId = $this->context->getPropertyFromAspect('workspace', 'id');
        $versioningWorkspaceId = is_int($workspaceId) || is_string($workspaceId) ? (int)$workspaceId : 0;
        if (empty($parentRecord[$inlineFieldName]) && $versioningWorkspaceId === 0) {
            return [];
        }

        $inlineFieldTca = $this->getInlineFieldTca($inlineFieldName, $parentTable);
        $inlineFieldConfig = $inlineFieldTca['config'] ?? [];
        $foreignTable = $inlineFieldConfig['foreign_table'] ?? null;
        if (!is_string($foreignTable) || $foreignTable === '') {
            throw new \Exception(
                sprintf('Could not load inline items for field "%s". Missing \'foreign_table\' in its TCA configuration', $inlineFieldName),
                1686299043
            );
        }
        $foreignField = $inlineFieldConfig['foreign_field'] ?? null;
        if (!is_string($foreignField) || $foreignField === '') {
            $foreignField = null;
        }
        $foreignTableCtrl = $this->getTableCtrl($foreignTable);
        $foreignSortby = $inlineFieldConfig['foreign_sortby'] ?? $foreignTableCtrl['sortby'] ?? null;
        if (!is_string($foreignSortby) || $foreignSortby === '') {
            $foreignSortby = null;
        }
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($foreignTable);
        $queryBuilder->getRestrictions()->removeAll();

        $parentRecordId = $parentRecord['_LOCALIZED_UID'] ?? $parentRecord['uid'] ?? 0;
        $constraints = $this->pageRepository->getDefaultConstraints($foreignTable);
        if (!empty($foreignField)) {
            $constraints['foreign_field'] = $queryBuilder->expr()->eq($foreignField, $queryBuilder->createNamedParameter($parentRecordId, Connection::PARAM_INT));
        } else {
            $itemsUidList = GeneralUtility::intExplode(',', (string)($parentRecord[$inlineFieldName] ?? ''), true);
            if (empty($itemsUidList)) {
                return [];
            }
            $constraints['uidList'] = $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($itemsUidList, ArrayParameterType::INTEGER));
        }
        $languageField = $foreignTableCtrl['languageField'] ?? null;
        if (is_string($languageField) && $languageField !== '') {
            $constraints['language'] = $queryBuilder->expr()->in($languageField, $queryBuilder->createNamedParameter([-1, $parentRecord['sys_language_uid'] ?? 0], ArrayParameterType::INTEGER));
        }
        $queryBuilder->select('*')->from($foreignTable)->where(...array_values($constraints));
        if ($foreignSortby) {
            $queryBuilder->orderBy($foreignSortby);
        }
        $queriedItems = $queryBuilder->executeQuery()->fetchAllAssociative();
        $processedItems = [];
        foreach ($queriedItems as $item) {
            // workspace overlay:
            $workspaceItem = $this->applyVersionOverlay($foreignTable, $item);
            if (!is_array($workspaceItem)) {
                continue;
            }
            $processedItems[] = $workspaceItem;
        }

        if (empty($foreignField)) {
            // sort items by uid list
            $itemsUidList = GeneralUtility::intExplode(',', (string)$parentRecord[$inlineFieldName], true);
            // sort $processedItems by the position of their uid in $itemsUidList
            usort($processedItems, static fn(array $itemA, array $itemB) => array_search($itemA['uid'], $itemsUidList) <=> array_search($itemB['uid'], $itemsUidList));
        } elseif (!empty($foreignSortby)) {
            // sort items again after workspace overlay
            usort($processedItems, static fn(array $itemA, array $itemB) => $itemA[$foreignSortby] <=> $itemB[$foreignSortby]);
        }

        return $processedItems;
    }

    /**
     * @param array<string, mixed> $item
     * @return mixed
     */
    private function applyVersionOverlay(string $table, array $item): mixed
    {
        $this->pageRepository->versionOL($table, $item);
        return $item;
    }

    /**
     * @return array<string, mixed>
     */
    private function getTableCtrl(string $table): array
    {
        $tableTca = $this->getGlobalTca()[$table] ?? null;
        if (!is_array($tableTca)) {
            return [];
        }

        $ctrl = $tableTca['ctrl'] ?? null;
        return is_array($ctrl) ? $ctrl : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function getGlobalTca(): array
    {
        $tca = $GLOBALS['TCA'] ?? null;
        return is_array($tca) ? $tca : [];
    }

    /**
     * @return array{config?: array<array-key, mixed>}
     */
    protected function getInlineFieldTca(string $inlineFieldName, string $localTableName = 'tt_content'): array
    {
        $tableTca = $this->getGlobalTca()[$localTableName] ?? null;
        $columns = is_array($tableTca) ? ($tableTca['columns'] ?? null) : null;
        $fieldTca = is_array($columns) ? ($columns[$inlineFieldName] ?? null) : null;
        if (!is_array($fieldTca)) {
            throw new \Exception('Tried to process inline records for non existing field ' . $localTableName . '.' . $inlineFieldName, 1587038305);
        }

        $config = $fieldTca['config'] ?? null;
        if (!is_array($config)) {
            return [];
        }

        return ['config' => $config];
    }
}
