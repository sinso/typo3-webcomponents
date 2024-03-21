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

    protected function renderSubComponent(string $dataProviderClassName, $additionalInputData = []): ?string
    {
        $dataProvider = GeneralUtility::makeInstance($dataProviderClassName);
        if (!$dataProvider instanceof DataProviderInterface) {
            throw new \RuntimeException('DataProvider must implement DataProviderInterface');
        }
        $dataProvider->setContentObjectRenderer($this->contentObjectRenderer);
        $webcomponentRenderingData = GeneralUtility::makeInstance(WebcomponentRenderingData::class);
        $webcomponentRenderingData->setAdditionalInputData($additionalInputData);
        $webcomponentRenderingData = $dataProvider->provide($webcomponentRenderingData);

        if (!$webcomponentRenderingData->isRenderable()) {
            return null;
        }

        return $this->renderComponent(
            $webcomponentRenderingData->getTagName(),
            $webcomponentRenderingData->getTagContent(),
            $webcomponentRenderingData->getTagProperties()
        );
    }
}
