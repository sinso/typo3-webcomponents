<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProvider\Traits;

use Sinso\Webcomponents\DataProvider\DataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait RenderSubComponent
{
    use ContentObjectRendererTrait;
    use RenderComponent;

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

        return $this->renderComponent($tagName, $content, $properties);
    }
}
