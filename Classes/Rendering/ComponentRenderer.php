<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Rendering;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\DataProviding\Traits\Assert;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\Events\ComponentEvaluated;
use Sinso\Webcomponents\Dto\Events\ComponentWasRendered;
use Sinso\Webcomponents\Dto\Events\ComponentWillBeRendered;
use Sinso\Webcomponents\Dto\InputData;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * @internal Render content elements via \Sinso\Webcomponents\ContentObject\WebcomponentContentObject.
 *           For rendering from PHP context use \Sinso\Webcomponents\DataProviding\Helpers\ComponentRenderingHelper.
 */
class ComponentRenderer
{
    use Assert;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function renderComponent(
        ComponentRenderingData $componentRenderingData,
        ContentObjectRenderer $contentObjectRenderer,
        string $componentClassName,
        ?TagBuilder $tagBuilder = null,
    ): string
    {
        $event = new ComponentWillBeRendered($componentRenderingData, $contentObjectRenderer, $componentClassName);
        $this->eventDispatcher->dispatch($event);

        $markup = $this->renderMarkup($event->getComponentRenderingData(), $tagBuilder);
        $componentWasRenderedEvent = new ComponentWasRendered(
            $markup,
            $event->getComponentRenderingData(),
            $contentObjectRenderer,
            $componentClassName,
        );
        $this->eventDispatcher->dispatch($componentWasRenderedEvent);
        return $componentWasRenderedEvent->getMarkup();
    }

    public function evaluateComponent(InputData $inputData, string $componentClassName, ?ContentObjectRenderer $contentObjectRenderer = null): ComponentRenderingData
    {
        if (!class_exists($componentClassName)) {
            throw new AssertionFailedException(
                'Configured component class "' . $componentClassName . '" does not exist',
                1729064011
            );
        }

        $component = GeneralUtility::makeInstance($componentClassName);
        $this->assert($component instanceof ComponentInterface, 'Configured component class "' . $componentClassName . '" must implement ' . ComponentInterface::class);
        /** @var ComponentInterface $component */
        if ($contentObjectRenderer === null) {
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $contentObjectRenderer->start([]);
        }
        if (method_exists($component, 'setContentObjectRenderer')) {
            $component->setContentObjectRenderer($contentObjectRenderer);
        }
        $componentRenderingData = $component->provide($inputData);
        $componentRenderingData = $componentRenderingData->withTagProperties(
            ArrayUtility::removeNullValuesRecursive($componentRenderingData->getTagProperties())
        );

        $event = new ComponentEvaluated($componentRenderingData, $contentObjectRenderer, $inputData, get_class($component));
        $this->eventDispatcher->dispatch($event);

        return $event->getComponentRenderingData();
    }

    private function renderMarkup(ComponentRenderingData $componentRenderingData, ?TagBuilder $tagBuilder = null): string
    {
        if ($componentRenderingData->getTagName() === null) {
            throw new AssertionFailedException('No tag name provided', 1730800497);
        }

        if ($tagBuilder === null) {
            $tagBuilder = GeneralUtility::makeInstance(TagBuilder::class);
        }
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
