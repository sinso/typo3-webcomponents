<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Tests\Unit\Dto;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sinso\Webcomponents\Dto\Markup;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Markup::class)]
class MarkupTest extends UnitTestCase
{
    #[Test]
    public function keepsHtmlAsIs(): void
    {
        self::assertSame('<b>bold</b> &amp; more', (string)new Markup('<b>bold</b> &amp; more'));
    }

    #[Test]
    public function joinEscapesStringsAndKeepsMarkup(): void
    {
        $markup = Markup::join(new Markup('<h2>'), '<script>"Tom" & \'Jerry\'</script>', null, new Markup('</h2>'));

        self::assertSame('<h2>&lt;script&gt;&quot;Tom&quot; &amp; &#039;Jerry&#039;&lt;/script&gt;</h2>', (string)$markup);
    }

    #[Test]
    public function joinWithoutPartsIsEmpty(): void
    {
        self::assertTrue(Markup::join()->isEmpty());
        self::assertTrue(Markup::join(null, '')->isEmpty());
        self::assertFalse(Markup::join('0')->isEmpty());
    }
}
