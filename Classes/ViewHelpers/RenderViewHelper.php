<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ViewHelpers;

use Sinso\Webcomponents\DataProvider\AssertionFailedException;
use Sinso\Webcomponents\DataProvider\Traits\RenderComponent;
use Sinso\Webcomponents\Dto\Events\WebComponentWillBeRendered;
use Sinso\Webcomponents\Dto\WebcomponentRenderingData;
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
        $this->registerArgument('dataProvider', 'string', 'Class name', true);
        $this->registerArgument('inputData', 'array', 'input data', false, []);
        $this->registerArgument('contentObjectRenderer', ContentObjectRenderer::class, 'current cObj');
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $webcomponentRenderingData = GeneralUtility::makeInstance(WebcomponentRenderingData::class);
        $webcomponentRenderingData->setAdditionalInputData($arguments['inputData']);
        if ($arguments['contentObjectRenderer'] instanceof ContentObjectRenderer) {
            $contentObjectRenderer = $arguments['contentObjectRenderer'];
        } else {
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $contentObjectRenderer->start([]);
        }
        $webcomponentRenderingData->setContentRecord($contentObjectRenderer->data);
        try {
            $webcomponentRenderingData = self::evaluateDataProvider($webcomponentRenderingData, $arguments['dataProvider'], $contentObjectRenderer);
        } catch (AssertionFailedException $e) {
            return $e->getRenderingPlaceholder();
        }

        $event = GeneralUtility::makeInstance(WebComponentWillBeRendered::class, $contentObjectRenderer, $webcomponentRenderingData);
        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
        $eventDispatcher->dispatch($event);

        if (!$webcomponentRenderingData->isRenderable()) {
            return '';
        }

        $tagName = $webcomponentRenderingData->getTagName();
        $content = $webcomponentRenderingData->getTagContent();
        $properties = $webcomponentRenderingData->getTagProperties();
        return self::renderComponent($tagName, $content, $properties);
    }
}
