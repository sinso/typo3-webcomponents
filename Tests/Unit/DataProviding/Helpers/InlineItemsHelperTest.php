<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Tests\Unit\DataProviding\Helpers;

use Sinso\Webcomponents\DataProviding\Helpers\InlineItemsHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(InlineItemsHelper::class)]
final class InlineItemsHelperTest extends UnitTestCase
{
    private InlineItemsHelper $subject;

    protected bool $resetSingletonInstances = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new InlineItemsHelper(
            self::createStub(ConnectionPool::class),
            self::createStub(Context::class),
            self::createStub(PageRepository::class),
        );
    }

    #[Test]
    public function throwsExceptionIfTcaIsNotLoadable(): void
    {
        self::expectExceptionCode(1587038305);

        $this->subject->loadInlineItems(['my_field' => 1], 'my_field', 'my_table');
    }

    #[Test]
    public function throwsExceptionIfFieldIsNotInline(): void
    {
        $GLOBALS['TCA'] = ArrayUtility::setValueByPath($GLOBALS['TCA'] ?? [], 'my_table/columns/my_field/config/type', 'text');

        self::expectExceptionCode(1686299043);

        $this->subject->loadInlineItems(['my_field' => 1], 'my_field', 'my_table');

        $GLOBALS['TCA'] = ArrayUtility::removeByPath($GLOBALS['TCA'], 'my_table/columns/my_field/config/type');
    }
}
