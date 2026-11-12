<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Tests\Unit\DataProviding\Helpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sinso\Webcomponents\DataProviding\Helpers\LinkHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\LinkFactory;
use TYPO3\CMS\Frontend\Typolink\LinkResult;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(LinkHelper::class)]
class LinkHelperTest extends UnitTestCase
{
    #[Test]
    public function linkTextPassedByTheCallerIsKeptAsIs(): void
    {
        $subject = $this->createSubject(static fn(string $linkText): string => $linkText);

        $linkResult = $subject->createLinkResult('t3://page?uid=1', 'Write &lt;b&gt; for <b>');

        self::assertSame('Write &lt;b&gt; for <b>', $linkResult->getLinkText());
    }

    #[Test]
    public function generatedLinkTextIsDecoded(): void
    {
        $subject = $this->createSubject(static fn(): string => htmlspecialchars('Tom & "Jerry" <3', ENT_QUOTES));

        $linkResult = $subject->createLinkResult('https://example.com');

        self::assertSame('Tom & "Jerry" <3', $linkResult->getLinkText());
    }

    /**
     * @param \Closure(string): string $linkTextBuilder simulates how the link builder derives the link text
     */
    private function createSubject(\Closure $linkTextBuilder): LinkHelper
    {
        $linkFactory = $this->createMock(LinkFactory::class);
        $linkFactory->method('create')->willReturnCallback(
            static fn(string $linkText): LinkResult => (new LinkResult('url', 'https://example.com'))
                ->withLinkText($linkTextBuilder($linkText)),
        );
        return new LinkHelper($this->createMock(ContentObjectRenderer::class), $linkFactory);
    }
}
