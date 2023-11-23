<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProvider\Traits;

use Sinso\Webcomponents\DataProvider\DataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

trait RenderSubComponent
{
    use ContentObjectRendererTrait;

    protected function renderSubComponent(string $dataProviderClassName, $inputData): ?string
    {
        $dataProvider = GeneralUtility::makeInstance($dataProviderClassName);
        if (!$dataProvider instanceof DataProviderInterface) {
            throw new \RuntimeException('DataProvider must implement DataProviderInterface');
        }
        $dataProvider->setContentObjectRenderer($this->contentObjectRenderer);
        $tagName = $dataProvider->getTagName();
        $properties = $dataProvider->getProperties($inputData);
        $content = $dataProvider->getContent($inputData);

        if ($tagName === null || $properties === null) {
            return null;
        }

        // Render
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
