<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProvider\Traits;

use Sinso\Webcomponents\DataProvider\DataProviderInterface;
use Sinso\Webcomponents\Dto\WebcomponentRenderingData;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait RenderSubComponent
{
    use ContentObjectRendererTrait;
    use RenderComponent;

    protected function renderSubComponent(string $dataProviderClassName, $inputData = []): ?string
    {
        $dataProvider = GeneralUtility::makeInstance($dataProviderClassName);
        if (!$dataProvider instanceof DataProviderInterface) {
            throw new \RuntimeException('DataProvider must implement DataProviderInterface');
        }
        $dataProvider->setContentObjectRenderer($this->contentObjectRenderer);
        $webComponentRenderingData = GeneralUtility::makeInstance(WebcomponentRenderingData::class);
        $webComponentRenderingData = $dataProvider->provide($inputData, $webComponentRenderingData);

        if (!$webComponentRenderingData->isRenderable()) {
            return null;
        }

        return $this->renderComponent(
            $webComponentRenderingData->getTagName(),
            $webComponentRenderingData->getContent(),
            $webComponentRenderingData->getProperties()
        );
    }
}
