<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Rendering;

use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\DataProviding\Traits\Assert;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\InputData;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class ComponentRenderer
{
    use Assert;

    /**
     * @param array<string, mixed> $properties
     */
    public function renderComponent(string $tagName, ?string $content, array $properties): string
    {
        /** @var TagBuilder $tagBuilder */
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
            $tagBuilder->addAttribute($key, (string)$value);
        }
        $tagBuilder->forceClosingTag(true);
        return $tagBuilder->render();
    }

    /**
     * @param class-string<ComponentInterface> $componentClassName
     */
    public function evaluateComponent(InputData $inputData, string $componentClassName, ?ContentObjectRenderer $contentObjectRenderer): ComponentRenderingData
    {
        /** @var ComponentInterface $component */
        $component = GeneralUtility::makeInstance($componentClassName);
        $this->assert($component instanceof ComponentInterface, 'Component must implement ComponentInterface');
        if ($contentObjectRenderer === null) {
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $contentObjectRenderer->start([]);
        }
        $component->setContentObjectRenderer($contentObjectRenderer);
        return $component->provide($inputData);
    }
}
