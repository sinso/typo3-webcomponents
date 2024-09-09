<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ViewHelpers;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\Events\ComponentWillBeRendered;
use Sinso\Webcomponents\Dto\InputData;
use Sinso\Webcomponents\Rendering\ComponentRenderer;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class RenderViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('component', 'string', 'Class name', true);
        $this->registerArgument('contentAs', 'string', 'If set the content will be made available in the additionalData property under the given key');
        $this->registerArgument('contentObjectRenderer', ContentObjectRenderer::class, 'current cObj');
        $this->registerArgument('inputData', 'array', 'input data', false, []);
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        /** @var ComponentRenderer $componentRenderer */
        $componentRenderer = GeneralUtility::makeInstance(ComponentRenderer::class);
        if ($arguments['contentObjectRenderer'] instanceof ContentObjectRenderer) {
            $contentObjectRenderer = $arguments['contentObjectRenderer'];
        } else {
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $contentObjectRenderer->start([]);
        }
        $inputData = self::gatherInputData($contentObjectRenderer, $arguments, $renderChildrenClosure);
        /** @var class-string<ComponentInterface> $componentClassName */
        $componentClassName = $arguments['component'];
        try {
            $componentRenderingData = $componentRenderer->evaluateComponent($inputData, $componentClassName, $contentObjectRenderer);
        } catch (AssertionFailedException $e) {
            return $e->getRenderingPlaceholder();
        }

        return self::renderComponent($contentObjectRenderer, $componentRenderingData);
    }

    /**
     * @param array<string, mixed> $arguments
     */
    private static function gatherInputData(ContentObjectRenderer $contentObjectRenderer, array $arguments, \Closure $renderChildrenClosure): InputData
    {
        if (!empty($arguments['contentAs'])) {
            $arguments['inputData'][$arguments['contentAs']] = $renderChildrenClosure();
        }
        return GeneralUtility::makeInstance(
            InputData::class,
            $contentObjectRenderer->data,
            $contentObjectRenderer->getCurrentTable(),
            $arguments['inputData']
        );
    }

    private static function renderComponent(ContentObjectRenderer $contentObjectRenderer, ComponentRenderingData $componentRenderingData): string
    {
        /** @var ComponentRenderer $componentRenderer */
        $componentRenderer = GeneralUtility::makeInstance(ComponentRenderer::class);

        $event = GeneralUtility::makeInstance(ComponentWillBeRendered::class, $contentObjectRenderer, $componentRenderingData);
        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
        $eventDispatcher->dispatch($event);

        $tagName = $componentRenderingData->getTagName();
        if ($tagName === null) {
            $e = new AssertionFailedException('No tag name provided', 1722689282);
            return $e->getRenderingPlaceholder();
        }
        $content = $componentRenderingData->getTagContent();
        $properties = $componentRenderingData->getTagProperties();
        return $componentRenderer->renderComponent($tagName, $content, $properties);
    }
}
