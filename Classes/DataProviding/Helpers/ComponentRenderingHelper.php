<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\InputData;
use Sinso\Webcomponents\Rendering\ComponentRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class ComponentRenderingHelper
{
    public function __construct(
        private readonly ComponentRenderer $componentRenderer,
    ) {}

    /**
     * @param class-string<ComponentInterface> $componentClassName
     */
    public function evaluateComponent(string $componentClassName, ?InputData $inputData = null, ?string $slot = null): ComponentRenderingData
    {
        $component = GeneralUtility::makeInstance($componentClassName);
        if (!$component instanceof ComponentInterface) {
            throw new \RuntimeException('Component must implement ComponentInterface', 1729064021);
        }
        if ($inputData === null) {
            $inputData = new InputData();
        }
        /** @var ContentObjectRenderer $contentObjectRenderer */
        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $contentObjectRenderer->start($inputData->record, $inputData->tableName);
        $component->setContentObjectRenderer($contentObjectRenderer);
        $componentRenderingData = $component->provide($inputData);

        if ($slot !== null) {
            $componentRenderingData->setTagProperty('slot', $slot);
        }

        return $componentRenderingData;
    }

    /**
     * @param class-string<ComponentInterface> $componentClassName
     */
    public function evaluateAndRenderComponent(string $componentClassName, ?InputData $inputData = null, ?string $slot = null): string
    {
        if ($inputData === null) {
            $inputData = new InputData();
        }
        $componentRenderingData = $this->evaluateComponent($componentClassName, $inputData, $slot);
        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $contentObjectRenderer->start($inputData->record, $inputData->tableName);

        return $this->componentRenderer->renderComponent($componentRenderingData, $contentObjectRenderer);
    }
}
