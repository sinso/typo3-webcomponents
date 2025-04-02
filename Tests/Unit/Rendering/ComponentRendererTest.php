<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Tests\Unit\Rendering;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\Events\ComponentWillBeRendered;
use Sinso\Webcomponents\Rendering\ComponentRenderer;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ComponentRenderer::class)]
class ComponentRendererTest extends UnitTestCase
{
    private ComponentRenderer $subject;
    private EventDispatcherInterface&MockObject $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->subject = new ComponentRenderer($this->eventDispatcher);
    }

    #[Test]
    public function rendersComponentAccordingToProvidedData(): void
    {
        $componentRenderer = (new ComponentRenderingData())
            ->withTagName('my-element')
            ->withTagProperties(['class' => 'foo'])
            ->withTagContent('bar');
        $result = $this->subject->renderComponent($componentRenderer, $this->createMock(ContentObjectRenderer::class));

        // should roughly look like: <my-element class="foo">bar</my-element>, but don't care about whitespaces
        self::assertMatchesRegularExpression(
            '/<my-element\s+class="foo">\s*bar\s*<\/my-element>/',
            $result,
        );
    }

    #[Test]
    public function inputFromComponentWillBeRenderedEventListenersIsConsidered(): void
    {
        $this->eventDispatcher->expects(self::once())->method('dispatch')->willReturnCallback(function ($event) {
            /** @var ComponentWillBeRendered $event */
            self::assertInstanceOf(ComponentWillBeRendered::class, $event);
            $event->setComponentRenderingData(
                $event->getComponentRenderingData()
                    ->withTagProperty('class', 'baz')
            );
            return $event;
        });

        $componentRenderer = (new ComponentRenderingData())
            ->withTagName('my-element')
            ->withTagProperties(['class' => 'foo'])
            ->withTagContent('bar');
        $result = $this->subject->renderComponent($componentRenderer, $this->createMock(ContentObjectRenderer::class));

        // should roughly look like: <my-element class="baz">bar</my-element>, but don't care about whitespaces
        self::assertMatchesRegularExpression(
            '/<my-element\s+class="baz">\s*bar\s*<\/my-element>/',
            $result,
        );
    }
}
