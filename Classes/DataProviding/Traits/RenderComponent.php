<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Traits;

use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

trait RenderComponent
{
    /**
     * @param array<string, mixed> $properties
     */
    protected static function renderComponent(string $tagName, ?string $content, array $properties): string
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

    private static function evaluateComponent(ComponentRenderingData $componentRenderingData, string $componentClassName, ?ContentObjectRenderer $contentObjectRenderer): ComponentRenderingData
    {
        if (empty($componentClassName)) {
            return $componentRenderingData;
        }
        /** @phpstan-ignore-next-line */
        $component = GeneralUtility::makeInstance($componentClassName);
        if (!$component instanceof ComponentInterface) {
            return $componentRenderingData;
        }
        if ($contentObjectRenderer === null) {
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $contentObjectRenderer->start([]);
        }
        $component->setContentObjectRenderer($contentObjectRenderer);
        return $component->provide($componentRenderingData);
    }
}
