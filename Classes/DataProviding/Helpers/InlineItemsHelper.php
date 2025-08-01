<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use Doctrine\DBAL\ArrayParameterType;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
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
     * @param array<string, string|integer> $parentRecord
     * @return list<array<string, string|integer>>
     */
    public function loadInlineItems(array $parentRecord, string $inlineFieldName, string $parentTable = 'tt_content'): array
    {
        // if the field is empty, and we're not in a workspace context, then there are no inline items
        /** @var int|string $versioningWorkspaceId */
        $versioningWorkspaceId = $this->context->getPropertyFromAspect('workspace', 'id');
        $versioningWorkspaceId = (int)$versioningWorkspaceId;
        if (empty($parentRecord[$inlineFieldName]) && $versioningWorkspaceId === 0) {
            return [];
        }

        $inlineFieldTca = $this->getInlineFieldTca($inlineFieldName, $parentTable);
        $foreignTable = $inlineFieldTca['config']['foreign_table'] ?? null;
        if ($foreignTable === null) {
            throw new \Exception(
                sprintf('Could not load inline items for field "%s". Missing \'foreign_table\' in its TCA configuration', $inlineFieldName),
                1686299043
            );
        }
        $foreignField = $inlineFieldTca['config']['foreign_field'] ?? null;
        $foreignTableTca = $GLOBALS['TCA'][$foreignTable];
        $foreignSortby = $inlineFieldTca['config']['foreign_sortby'] ?? $foreignTableTca['ctrl']['sortby'] ?? null;
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($foreignTable);
        $queryBuilder->getRestrictions()->removeAll();

        $parentRecordId = $parentRecord['_LOCALIZED_UID'] ?? $parentRecord['uid'] ?? 0;
        $constraints = [];
        if (!empty($foreignField)) {
            $constraints[] = $queryBuilder->expr()->eq($foreignField, $queryBuilder->createNamedParameter($parentRecordId, Connection::PARAM_INT));
        } else {
            $itemsUidList = GeneralUtility::intExplode(',', (string)($parentRecord[$inlineFieldName] ?? ''), true);
            if (empty($itemsUidList)) {
                return [];
            }
            $constraints[] = $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($itemsUidList, ArrayParameterType::INTEGER));
        }
        if (isset($foreignTableTca['ctrl']['languageField'])) {
            $constraints[] = $queryBuilder->expr()->in($foreignTableTca['ctrl']['languageField'], $queryBuilder->createNamedParameter([-1, $parentRecord['sys_language_uid'] ?? 0], ArrayParameterType::INTEGER));
        }
        $constraints[] = QueryHelper::stripLogicalOperatorPrefix($this->pageRepository->enableFields($foreignTable));
        $queryBuilder->select('*')->from($foreignTable)->where(...$constraints);
        if ($foreignSortby) {
            $queryBuilder->orderBy($foreignSortby);
        }
        $queriedItems = $queryBuilder->executeQuery()->fetchAllAssociative();
        $processedItems = [];
        foreach ($queriedItems as $item) {
            // workspace overlay:
            $this->pageRepository->versionOL($foreignTable, $item);
            if ($item === false) {
                continue;
            }
            $processedItems[] = $item;
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
     * @return array{config: array{foreign_table?: string, foreign_field?: string, foreign_sortby?: string}, label: string, type: string}
     */
    protected function getInlineFieldTca(string $inlineFieldName, string $localTableName = 'tt_content'): array
    {
        if (!isset($GLOBALS['TCA'][$localTableName]['columns'][$inlineFieldName])) {
            throw new \Exception('Tried to process inline records for non existing field ' . $localTableName . '.' . $inlineFieldName, 1587038305);
        }
        return $GLOBALS['TCA'][$localTableName]['columns'][$inlineFieldName];
    }
}
