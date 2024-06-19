<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ViewHelpers;

use Sinso\Webcomponents\DataProvider\DataProviderInterface;
use Sinso\Webcomponents\Dto\Events\WebComponentWillBeRendered;
use Sinso\Webcomponents\Dto\WebcomponentRenderingData;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class RenderViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

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
            $webcomponentRenderingData->setContentRecord($arguments['contentObjectRenderer']->data);
        }
        $webcomponentRenderingData = self::evaluateDataProvider($webcomponentRenderingData, $arguments['dataProvider'], $arguments['contentObjectRenderer']);

        $event = GeneralUtility::makeInstance(WebComponentWillBeRendered::class, $webcomponentRenderingData);
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

    private static function evaluateDataProvider(WebcomponentRenderingData $webcomponentRenderingData, string $dataProviderClassName, ?ContentObjectRenderer $contentObjectRenderer): WebcomponentRenderingData
    {
        if (empty($dataProviderClassName)) {
            return $webcomponentRenderingData;
        }
        if ($contentObjectRenderer === null) {
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $contentObjectRenderer->start([]);
        }
        $dataProvider = GeneralUtility::makeInstance($dataProviderClassName);
        if ($dataProvider instanceof DataProviderInterface) {
            $dataProvider->setContentObjectRenderer($contentObjectRenderer);
            $webcomponentRenderingData = $dataProvider->provide($webcomponentRenderingData);
        }
        return $webcomponentRenderingData;
    }

    private static function renderComponent(string $tagName, ?string $content, array $properties): string
    {
        $tagBuilder = GeneralUtility::makeInstance(TagBuilder::class);
        $tagBuilder->setTagName($tagName);
        if (!empty($content)) {
            $tagBuilder->setContent($content);
        }
        foreach ($properties as $key => $value) {
            if ($value === null) {
                continue;
            }
            if (!is_scalar($value)) {
                $value = json_encode($value);
            }
            $tagBuilder->addAttribute($key, $value);
        }
        $tagBuilder->forceClosingTag(true);
        return $tagBuilder->render();
    }
}
