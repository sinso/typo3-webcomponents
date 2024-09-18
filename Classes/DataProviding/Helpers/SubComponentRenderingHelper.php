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

class SubComponentRenderingHelper
{
    public function __construct(
        private readonly ComponentRenderer $componentRenderer,
    ) {}

    /**
     * @param class-string<ComponentInterface> $componentClassName
     * @param array<string, mixed> $additionalInputData
     */
    public function evaluateSubComponent(string $componentClassName, array $additionalInputData = [], ?string $slot = null): ?ComponentRenderingData
    {
        $component = GeneralUtility::makeInstance($componentClassName);
        if (!$component instanceof ComponentInterface) {
            throw new \RuntimeException('Component must implement ComponentInterface');
        }
        /** @var ContentObjectRenderer $contentObjectRenderer */
        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $contentObjectRenderer->start([]);
        $component->setContentObjectRenderer($contentObjectRenderer);
        $inputData = GeneralUtility::makeInstance(InputData::class, [], '', $additionalInputData);
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
     * @param array<string, mixed> $additionalInputData
     */
    public function renderSubComponent(string $componentClassName, array $additionalInputData = [], ?string $slot = null): ?string
    {
        $componentRenderingData = $this->evaluateSubComponent($componentClassName, $additionalInputData, $slot);
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
