<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Tests\Unit\Dto;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\Markup;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ComponentRenderingData::class)]
class ComponentRenderingDataTest extends UnitTestCase
{
    #[Test]
    public function stringTagContentIsEscaped(): void
    {
        $data = (new ComponentRenderingData())->withTagContent('<img src=x onerror=alert(1)> & co');

        self::assertSame('&lt;img src=x onerror=alert(1)&gt; &amp; co', (string)$data->getTagContent());
    }

    #[Test]
    public function markupTagContentIsKeptAsIs(): void
    {
        $markup = new Markup('<h2>Headline</h2>');
        $data = (new ComponentRenderingData())->withTagContent($markup);

        self::assertSame($markup, $data->getTagContent());
    }

    #[Test]
    public function nullRemovesTagContent(): void
    {
        $data = (new ComponentRenderingData())->withTagContent('text')->withTagContent(null);

        self::assertNull($data->getTagContent());
    }
}
