<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Tests\Functional\DataProviding\Helpers;

use Sinso\Webcomponents\DataProviding\Helpers\InlineItemsHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\WorkspaceAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(InlineItemsHelper::class)]
final class InlineItemsHelperTest extends FunctionalTestCase
{
    private const string INLINE_FIELD = 'inline_2';
    private const string PARENT_TABLE = 'tx_inlineitemshelperfixture_parent';
    private const string CHILD_TABLE = 'tx_inlineitemshelperfixture_child';

    private InlineItemsHelper $subject;

    protected array $testExtensionsToLoad = [
        __DIR__ . '/../../Fixtures/Extensions/inline_items_helper_fixture',
        'typo3conf/ext/webcomponents',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setWorkspace(0);
        $this->subject = new InlineItemsHelper(
            $this->get(ConnectionPool::class),
            $this->get(Context::class),
            $this->get(PageRepository::class),
        );
    }

    #[Test]
    public function returnsEmptyArrayForNoData(): void
    {
        $items = $this->subject->loadInlineItems(['pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertSame([], $items);
    }

    #[Test]
    public function returnsEmptyArrayForNoDataInWorkspace(): void
    {
        $this->setWorkspace(1);

        $items = $this->subject->loadInlineItems(['pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertSame([], $items);
    }

    #[Test]
    public function returnsOneItemForOneRecordInDatabase(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/OneInlineItem.csv');

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1, self::INLINE_FIELD => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertIsArray($items);
        self::assertCount(1, $items);
        self::assertIsArray($items[0]);
    }

    #[Test]
    public function returnsRecordFromDatabase(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/OneInlineItem.csv');

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1, self::INLINE_FIELD => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        $connection = $this->getConnectionPool()->getConnectionForTable(self::CHILD_TABLE);
        $rows = $connection->executeQuery(
            'SELECT * FROM ' . self::CHILD_TABLE . ' WHERE parentid = :parentid',
            ['parentid' => 1]
        )->fetchAllAssociative();

        self::assertIsArray($items);
        self::assertCount(1, $items);
        self::assertSame($rows, $items);
    }

    #[Test]
    public function returnsSortedItems(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/MultipleInlineItems.csv');

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1, self::INLINE_FIELD => 2], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertIsArray($items);
        self::assertCount(2, $items);
        self::assertSame(2, $items[0]['uid']);
        self::assertSame(1, $items[1]['uid']);
    }

    #[Test]
    public function returnsOneItemWithOneRecordCreatedInWorkspace(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/OneInlineItemInWorkspace.csv');

        $this->setWorkspace(1);

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertIsArray($items);
        self::assertCount(1, $items);
        self::assertIsArray($items[0]);
    }

    #[Test]
    public function returnSortedItemsInWorkspace(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/MultipleInlineItemsInWorkspace.csv');

        $this->setWorkspace(1);

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertIsArray($items);
        self::assertCount(2, $items);
        self::assertSame(2, $items[0]['uid']);
        self::assertSame(1, $items[1]['uid']);
    }

    #[Test]
    public function returnSortedItemsWithCreatedOneAtTheBeginningInWorkspace(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/MultipleInlineItemsInWorkspaceWithNewItemAtTheBeginning.csv');

        $this->setWorkspace(1);

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertIsArray($items);
        self::assertCount(3, $items);
        self::assertSame(3, $items[0]['uid']);
        self::assertSame(1, $items[1]['uid']);
        self::assertSame(2, $items[2]['uid']);
    }

    #[Test]
    public function returnSortedItemsWithCreatedOneInTheMiddleInWorkspace(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/MultipleInlineItemsInWorkspaceWithNewItemInTheMiddle.csv');

        $this->setWorkspace(1);

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertIsArray($items);
        self::assertCount(3, $items);
        self::assertSame(1, $items[0]['uid']);
        self::assertSame(3, $items[1]['uid']);
        self::assertSame(2, $items[2]['uid']);
    }

    #[Test]
    public function returnSortedItemsWithCreatedOneAtTheEndInWorkspace(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/MultipleInlineItemsInWorkspaceWithNewItemAtTheEnd.csv');

        $this->setWorkspace(1);

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertIsArray($items);
        self::assertCount(3, $items);
        self::assertSame(1, $items[0]['uid']);
        self::assertSame(2, $items[1]['uid']);
        self::assertSame(3, $items[2]['uid']);
    }

    #[Test]
    public function returnSortedItemsWithRemovedItemInWorkspace(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/MultipleInlineItemsInWorkspaceWithRemovedItem.csv');

        $this->setWorkspace(1);

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertIsArray($items);
        self::assertCount(1, $items);
        self::assertSame(2, $items[0]['uid']);
    }

    #[Test]
    public function returnsItemChangedInWorkspace(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/OneInlineItemChangedInWorkspace.csv');

        $this->setWorkspace(1);

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertIsArray($items);
        self::assertCount(1, $items);
        self::assertSame('BJB/changed', $items[0]['input_1']);
    }

    #[Test]
    public function returnsSortedItemsWithSortingChangedInWorkspace(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/MultipleInlineItemsRearrangedInWorkspace.csv');

        $this->setWorkspace(1);

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertIsArray($items);
        self::assertCount(2, $items);
        self::assertSame('BJB/second', $items[0]['input_1']);
        self::assertSame('BJB/first', $items[1]['input_1']);
    }

    #[Test]
    public function returnsItemEnabledInWorkspace(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/OneInlineItemEnabledInWorkspace.csv');

        $this->setWorkspace(1);

        $items = $this->subject->loadInlineItems(['uid' => 1, 'pid' => 1], self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertCount(1, $items);
    }

    #[Test]
    public function returnsOverlaidItemWhenParentRecordIsTranslated(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/TranslatedInlineItem.csv');
        $record = [
            'uid' => 1,
            'pid' => 1,
            'sys_language_uid' => 1,
            self::INLINE_FIELD => 1,
            '_LOCALIZED_UID' => 2,
        ];

        $items = $this->subject->loadInlineItems($record, self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertCount(1, $items);
        self::assertSame('BJB/overlaid', $items[0]['input_1']);
    }

    #[Test]
    public function returnsItemWithoutDefaultTranslationWhenContentElementIsTranslated(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/OneInlineItemWithoutDefaultTranslation.csv');
        $record = [
            'uid' => 1,
            'pid' => 1,
            'sys_language_uid' => 1,
            self::INLINE_FIELD => 1,
            '_LOCALIZED_UID' => 2,
        ];

        $items = $this->subject->loadInlineItems($record, self::INLINE_FIELD, self::PARENT_TABLE);

        self::assertCount(1, $items);
        self::assertSame('BJB', $items[0]['input_1']);
    }

    private function setWorkspace(int $workspaceId): void
    {
        $context = GeneralUtility::makeInstance(Context::class);
        $context->setAspect('workspace', new WorkspaceAspect($workspaceId));
    }
}
