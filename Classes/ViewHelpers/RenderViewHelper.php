<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ViewHelpers;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\Traits\RenderComponent;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\Events\ComponentWillBeRendered;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class RenderViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;
    use RenderComponent;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('component', 'string', 'Class name', true);
        $this->registerArgument('inputData', 'array', 'input data', false, []);
        $this->registerArgument('contentObjectRenderer', ContentObjectRenderer::class, 'current cObj');
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $componentRenderingData = GeneralUtility::makeInstance(ComponentRenderingData::class);
        assert(is_array($arguments['inputData']));
        $componentRenderingData->setAdditionalInputData($arguments['inputData']);
        if ($arguments['contentObjectRenderer'] instanceof ContentObjectRenderer) {
            $contentObjectRenderer = $arguments['contentObjectRenderer'];
        } else {
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $contentObjectRenderer->start([]);
        }
        $componentRenderingData->setContentRecord($contentObjectRenderer->data);
        assert(is_string($arguments['component']));
        try {
            $componentRenderingData = self::evaluateComponent($componentRenderingData, $arguments['component'], $contentObjectRenderer);
        } catch (AssertionFailedException $e) {
            return $e->getRenderingPlaceholder();
        }

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
        return self::renderComponent($tagName, $content, $properties);
    }
}
