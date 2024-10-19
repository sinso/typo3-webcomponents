<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\DataProviding\Traits\ContentObjectRendererTrait;
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
    public function evaluateComponent(string $componentClassName, ?InputData $inputData = null, ?string $slot = null): ?ComponentRenderingData
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
        try {
            $componentRenderingData = $component->provide($inputData);
        } catch (AssertionFailedException) {
            return null;
        }

        if ($slot !== null) {
            $componentRenderingData->setTagProperty('slot', $slot);
        }

        return $componentRenderingData;
    }

    /**
     * @param class-string<ComponentInterface> $componentClassName
     */
    public function renderComponent(string $componentClassName, ?InputData $inputData = null, ?string $slot = null): ?string
    {
        $componentRenderingData = $this->evaluateComponent($componentClassName, $inputData, $slot);
        if ($componentRenderingData === null) {
            return null;
        }

        $properties = $componentRenderingData->getTagProperties();

        $tagName = $componentRenderingData->getTagName();
        if ($tagName === null) {
            return null;
        }

        return $this->componentRenderer->renderComponent(
            $tagName,
            $componentRenderingData->getTagContent(),
            $properties,
        );
    }
}
