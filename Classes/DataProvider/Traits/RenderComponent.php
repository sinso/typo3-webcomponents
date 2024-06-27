<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProvider\Traits;

use Sinso\Webcomponents\DataProvider\DataProviderInterface;
use Sinso\Webcomponents\Dto\WebcomponentRenderingData;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

trait RenderComponent
{
    protected static function renderComponent(string $tagName, ?string $content, array $properties): string
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
}
