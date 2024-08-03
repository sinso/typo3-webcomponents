<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\Traits\RenderComponent;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\Events\ComponentWillBeRendered;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

abstract class AbstractWebcomponentContentObject extends AbstractContentObject
{
    use RenderComponent;

    protected EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array<string, mixed> $conf
     */
    public function render($conf = []): string
    {
        /** @var ComponentRenderingData $componentRenderingData */
        $componentRenderingData = GeneralUtility::makeInstance(ComponentRenderingData::class);
        if ($this->cObj?->getCurrentTable() === 'tt_content') {
            $componentRenderingData->setContentRecord($this->cObj->data);
        }

        foreach ($this->getDataProvidingStrategies() as $strategy) {
            try {
                $componentRenderingData = $strategy($componentRenderingData, $conf);
            } catch (AssertionFailedException $e) {
                return $e->getRenderingPlaceholder();
            }
        }

        $event = GeneralUtility::makeInstance(ComponentWillBeRendered::class, $this->cObj, $componentRenderingData);
        try {
            $this->eventDispatcher->dispatch($event);
            // render with tag builder
            $markup = $this->renderMarkup($componentRenderingData);
        } catch (AssertionFailedException $e) {
            return $e->getRenderingPlaceholder();
        }

        // apply stdWrap
        if (is_array($conf['stdWrap.'] ?? null)) {
            $markup = $this->cObj?->stdWrap($markup, $conf['stdWrap.']) ?? $markup;
        }

        return $markup;
    }

    private function renderMarkup(ComponentRenderingData $componentRenderingData): string
    {
        $tagName = $componentRenderingData->getTagName();
        if ($tagName === null) {
            throw new AssertionFailedException('No tag name provided', 1722672898);
        }
        $content = $componentRenderingData->getTagContent();
        $properties = $componentRenderingData->getTagProperties();

        return self::renderComponent($tagName, $content, $properties);
    }

    /**
     * @return iterable<callable>
     */
    abstract protected function getDataProvidingStrategies(): iterable;
}
