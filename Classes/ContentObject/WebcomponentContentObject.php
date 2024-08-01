<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\Traits\RenderComponent;
use Sinso\Webcomponents\Dto\Events\ComponentFolderIsApplied;
use Sinso\Webcomponents\Dto\Events\ComponentWillBeRendered;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

class WebcomponentContentObject extends AbstractContentObject
{
    use RenderComponent;

    public function __construct(
       private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    public function render($conf = []): string
    {
        $componentRenderingData = GeneralUtility::makeInstance(ComponentRenderingData::class);
        if ($this->cObj->getCurrentTable() === 'tt_content') {
            $componentRenderingData->setContentRecord($this->cObj->data);
        }

        $componentFolder = $this->cObj->stdWrapValue('componentFolder', $conf);
        try {
            $componentRenderingData = $this->applyComponentFolder($componentRenderingData, $componentFolder);
            $event = GeneralUtility::makeInstance(ComponentWillBeRendered::class, $this->cObj, $componentRenderingData);
            $this->eventDispatcher->dispatch($event);
        } catch (AssertionFailedException $e) {
            return $e->getRenderingPlaceholder();
        }

        // render with tag builder
        $markup = $this->renderMarkup($componentRenderingData);

        // apply stdWrap
        $markup = $this->cObj->stdWrap($markup, $conf['stdWrap.'] ?? []);

        return $markup;
    }

    private function applyComponentFolder(ComponentRenderingData $componentRenderingData, string $componentFolder): ComponentRenderingData
    {
        $event = GeneralUtility::makeInstance(ComponentFolderIsApplied::class, $componentRenderingData, $this->cObj, $componentFolder);
        $this->eventDispatcher->dispatch($event);
        return $componentRenderingData;
    }

    private function renderMarkup(ComponentRenderingData $componentRenderingData): string
    {
        $tagName = $componentRenderingData->getTagName();
        $content = $componentRenderingData->getTagContent();
        $properties = $componentRenderingData->getTagProperties();

        return self::renderComponent($tagName, $content, $properties);
    }
}
