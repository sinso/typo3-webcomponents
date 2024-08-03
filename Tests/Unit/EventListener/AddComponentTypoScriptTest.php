<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Tests\Unit\EventListener;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ServerRequestInterface;
use Sinso\Webcomponents\EventListener\AddComponentTypoScript;
use Sinso\Webcomponents\Registry\ComponentRegistry;
use Sinso\Webcomponents\TypoScript\ComponentTypoScriptGenerator;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\AfterTemplatesHaveBeenDeterminedEvent;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(AddComponentTypoScript::class)]
class AddComponentTypoScriptTest extends UnitTestCase
{
    /**
     * @var array<string, mixed>
     */
    private const EXISTING_TEMPLATE_ROW = ['uid' => 1, 'title' => 'Existing template', 'config' => 'page = PAGE'];

    #[Test]
    public function addsTheComponentTypoScriptAfterAllOtherTemplates(): void
    {
        $event = $this->createEvent([self::EXISTING_TEMPLATE_ROW]);

        ($this->createSubject(['image' => 'Acme\\Components\\Image']))($event);

        $templateRows = $event->getTemplateRows();
        self::assertCount(2, $templateRows);
        self::assertSame(self::EXISTING_TEMPLATE_ROW, $templateRows[0]);
        self::assertSame(
            "tt_content.image >\ntt_content.image = WEBCOMPONENT\ntt_content.image.component = Acme\\Components\\Image\n",
            $templateRows[1]['config']
        );
        self::assertSame(0, $templateRows[1]['clear']);
        self::assertSame(0, $templateRows[1]['root']);
    }

    #[Test]
    public function addsNoTemplateIfNoComponentsAreRegistered(): void
    {
        $event = $this->createEvent([self::EXISTING_TEMPLATE_ROW]);

        ($this->createSubject([]))($event);

        self::assertSame([self::EXISTING_TEMPLATE_ROW], $event->getTemplateRows());
    }

    #[Test]
    public function addsNoTemplateIfThePageTreeHasNoTypoScriptAtAll(): void
    {
        $event = $this->createEvent([], $this->createSite([]));

        ($this->createSubject(['image' => 'Acme\\Components\\Image']))($event);

        self::assertSame([], $event->getTemplateRows());
    }

    #[Test]
    public function addsTheComponentTypoScriptToASiteWithoutTemplateRecords(): void
    {
        $event = $this->createEvent([], $this->createSite(['typo3/fluid-styled-content']));

        ($this->createSubject(['image' => 'Acme\\Components\\Image']))($event);

        self::assertCount(1, $event->getTemplateRows());
    }

    /**
     * @param array<string, class-string> $componentsByCType
     */
    private function createSubject(array $componentsByCType): AddComponentTypoScript
    {
        return new AddComponentTypoScript(
            new ComponentRegistry($componentsByCType),
            new ComponentTypoScriptGenerator(),
        );
    }

    /**
     * @param list<array<string, mixed>> $templateRows
     */
    private function createEvent(array $templateRows, ?Site $site = null): AfterTemplatesHaveBeenDeterminedEvent
    {
        $request = null;
        if ($site !== null) {
            $request = (new ServerRequest())->withAttribute('site', $site);
        }
        /** @var ServerRequestInterface|null $request */
        return new AfterTemplatesHaveBeenDeterminedEvent([], $request, $templateRows);
    }

    /**
     * @param list<string> $sets
     */
    private function createSite(array $sets): Site
    {
        return new Site('acme', 1, ['base' => 'https://example.com/', 'dependencies' => $sets]);
    }
}
