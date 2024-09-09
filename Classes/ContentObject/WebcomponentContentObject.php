<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\Events\ComponentWillBeRendered;
use Sinso\Webcomponents\Dto\InputData;
use Sinso\Webcomponents\Rendering\ComponentRenderer;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

class WebcomponentContentObject extends AbstractContentObject
{
    public function __construct(
        private readonly ComponentRenderer $componentRenderer,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @param array<string, mixed> $conf
     */
    public function render($conf = []): string
    {
        $inputData = GeneralUtility::makeInstance(
            InputData::class,
            $this->cObj?->data ?? [],
            $this->cObj?->getCurrentTable() ?? '',
        );
        if (is_array($conf['additionalInputData.'] ?? null)) {
            // apply stdWrap to all additionalInputData properties
            foreach ($conf['additionalInputData.'] as $key => $value) {
                $key = (string)$key;
                if (!str_ends_with($key, '.')) {
                    continue;
                }
                $keyWithoutDot = substr($key, 0, -1);
                $conf['additionalInputData.'][$keyWithoutDot] = $this->cObj?->stdWrapValue($keyWithoutDot, $conf['additionalInputData.']);
                unset($conf['additionalInputData.'][$key]);
            }
            $inputData->additionalData = $conf['additionalInputData.'];
        }
        $componentClassName = $this->cObj?->stdWrapValue('component', $conf, null);
        if (!is_string($componentClassName)) {
            return '';
        }

        try {
            /** @var class-string<ComponentInterface> $componentClassName */
            $componentRenderingData = $this->componentRenderer->evaluateComponent($inputData, $componentClassName, $this->cObj);
        } catch (AssertionFailedException $e) {
            return $e->getRenderingPlaceholder();
        }
        $componentRenderingData = $this->evaluateTypoScriptConfiguration($componentRenderingData, $conf);

        $event = GeneralUtility::makeInstance(ComponentWillBeRendered::class, $this->cObj, $componentRenderingData);
        try {
            $this->eventDispatcher->dispatch($event);
            // render with tag builder
            $markup = $this->renderMarkup($componentRenderingData);
        } catch (AssertionFailedException $e) {
            return $e->getRenderingPlaceholder();
        }

        // apply stdWrap
        if (is_array($conf['stdWrap.'] ?? null)) {
            $markup = $this->cObj?->stdWrap($markup, $conf['stdWrap.']) ?? $markup;
        }

        return $markup;
    }

    /**
     * @param array<string, mixed> $conf
     */
    private function evaluateTypoScriptConfiguration(ComponentRenderingData $componentRenderingData, array $conf): ComponentRenderingData
    {
        if (is_array($conf['properties.'] ?? null)) {
            foreach ($conf['properties.'] as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $componentRenderingData->setTagProperty($key, $this->cObj?->cObjGetSingle($value, $conf['properties.'][$key . '.']));
            }
        }
        $tagName = $this->cObj?->stdWrapValue('tagName', $conf);
        if (is_string($tagName) && $tagName !== '') {
            $componentRenderingData->setTagName($tagName);
        }
        return $componentRenderingData;
    }

    private function renderMarkup(ComponentRenderingData $componentRenderingData): string
    {
        $tagName = $componentRenderingData->getTagName();
        if ($tagName === null) {
            throw new AssertionFailedException('No tag name provided', 1722672898);
        }
        $content = $componentRenderingData->getTagContent();
        $properties = $componentRenderingData->getTagProperties();

        return $this->componentRenderer->renderComponent($tagName, $content, $properties);
    }
}
