<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Rendering;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\DataProviding\Traits\Assert;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\Events\ComponentEvaluated;
use Sinso\Webcomponents\Dto\Events\ComponentWillBeRendered;
use Sinso\Webcomponents\Dto\InputData;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class ComponentRenderer
{
    use Assert;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function renderComponent(ComponentRenderingData $componentRenderingData, ContentObjectRenderer $contentObjectRenderer): string
    {
        $event = new ComponentWillBeRendered($componentRenderingData, $contentObjectRenderer);
        $this->eventDispatcher->dispatch($event);
        return $this->renderMarkup($componentRenderingData);
    }

    /**
     * @param class-string<ComponentInterface> $componentClassName
     */
    public function evaluateComponent(InputData $inputData, string $componentClassName, ?ContentObjectRenderer $contentObjectRenderer): ComponentRenderingData
    {
        /** @var ComponentInterface $component */
        $component = GeneralUtility::makeInstance($componentClassName);
        $this->assert($component instanceof ComponentInterface, 'Component must implement ComponentInterface');
        if ($contentObjectRenderer === null) {
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $contentObjectRenderer->start([]);
        }
        $component->setContentObjectRenderer($contentObjectRenderer);
        $componentRenderingData = $component->provide($inputData);
        $componentRenderingData->setTagProperties(
            ArrayUtility::removeNullValuesRecursive($componentRenderingData->getTagProperties())
        );

        $event = new ComponentEvaluated($componentRenderingData, $contentObjectRenderer, $inputData, $componentClassName);
        $this->eventDispatcher->dispatch($event);

        return $event->componentRenderingData;
    }

    private function renderMarkup(ComponentRenderingData $componentRenderingData): string
    {
        if ($componentRenderingData->getTagName() === null) {
            throw new AssertionFailedException('No tag name provided', 1730800497);
        }

        /** @var TagBuilder $tagBuilder */
        $tagBuilder = GeneralUtility::makeInstance(TagBuilder::class);
        $tagBuilder->setTagName($componentRenderingData->getTagName());
        if (!empty($componentRenderingData->getTagContent())) {
            $tagBuilder->setContent($componentRenderingData->getTagContent());
        }
        foreach ($componentRenderingData->getTagProperties() as $key => $value) {
            if (!is_scalar($value)) {
                $value = json_encode($value);
            }
            $tagBuilder->addAttribute($key, (string)$value);
        }
        $tagBuilder->forceClosingTag(true);
        return $tagBuilder->render();
    }
}
