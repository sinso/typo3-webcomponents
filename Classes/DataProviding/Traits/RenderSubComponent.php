<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Traits;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait RenderSubComponent
{
    use ContentObjectRendererTrait;
    use RenderComponent;

    protected function renderSubComponent(string $componentClassName, $additionalInputData = [], ?string $slot = null): ?string
    {
        $component = GeneralUtility::makeInstance($componentClassName);
        if (!$component instanceof ComponentInterface) {
            throw new \RuntimeException('Component must implement ComponentInterface');
        }
        $component->setContentObjectRenderer($this->contentObjectRenderer);
        $componentRenderingData = GeneralUtility::makeInstance(ComponentRenderingData::class);
        $componentRenderingData->setAdditionalInputData($additionalInputData);
        try {
            $componentRenderingData = $component->provide($componentRenderingData);
        } catch (AssertionFailedException) {
            return null;
        }

        if (!$componentRenderingData->isRenderable()) {
            return null;
        }

        $properties = $componentRenderingData->getTagProperties();
        if ($slot !== null) {
            $properties['slot'] = $slot;
        }

        return self::renderComponent(
            $componentRenderingData->getTagName(),
            $componentRenderingData->getTagContent(),
            $properties,
        );
    }
}
