<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\Traits\RenderComponent;
use Sinso\Webcomponents\Dto\Events\ComponentWillBeRendered;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

class WebcomponentContentObject extends AbstractContentObject
{
    use RenderComponent;

    /**
     * @param array<string, mixed> $conf
     */
    public function render($conf = []): string
    {
        $componentRenderingData = GeneralUtility::makeInstance(ComponentRenderingData::class);
        if ($this->cObj->getCurrentTable() === 'tt_content') {
            $componentRenderingData->setContentRecord($this->cObj->data);
        }
        if (isset($conf['additionalInputData.'])) {
            // apply stdWrap to all additionalInputData properties
            foreach ($conf['additionalInputData.'] as $key => $value) {
                $key = (string) $key;
                if (!str_ends_with($key, '.')) {
                    continue;
                }
                $keyWithoutDot = substr($key, 0, -1);
                $conf['additionalInputData.'][$keyWithoutDot] = $this->cObj->stdWrapValue($keyWithoutDot, $conf['additionalInputData.']);
                unset($conf['additionalInputData.'][$key]);
            }
            $componentRenderingData->setAdditionalInputData($conf['additionalInputData.']);
        }
        try {
            $componentRenderingData = self::evaluateComponent($componentRenderingData, $conf['component'] ?? '', $this->cObj);
        } catch (AssertionFailedException $e) {
            return $e->getRenderingPlaceholder();
        }
        $componentRenderingData = $this->evaluateTypoScriptConfiguration($componentRenderingData, $conf);

        $event = GeneralUtility::makeInstance(ComponentWillBeRendered::class, $this->cObj, $componentRenderingData);
        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
        $eventDispatcher->dispatch($event);

        if (!$componentRenderingData->isRenderable()) {
            return '';
        }

        // render with tag builder
        $markup = $this->renderMarkup($componentRenderingData);

        // apply stdWrap
        $markup = $this->cObj->stdWrap($markup, $conf['stdWrap.'] ?? []);

        return $markup;
    }

    /**
     * @param array<string, mixed> $conf
     */
    private function evaluateTypoScriptConfiguration(ComponentRenderingData $componentRenderingData, array $conf): ComponentRenderingData
    {
        if (isset($conf['properties.'])) {
            foreach ($conf['properties.'] as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $componentRenderingData->setTagProperty($key, $this->cObj->cObjGetSingle($value, $conf['properties.'][$key . '.']));
            }
        }
        if (($conf['tagName'] ?? '') || ($conf['tagName.'] ?? [])) {
            $componentRenderingData->setTagName($this->cObj->stdWrap($conf['tagName'] ?? '', $conf['tagName.'] ?? []) ?: null);
        }
        return $componentRenderingData;
    }

    private function renderMarkup(ComponentRenderingData $componentRenderingData): string
    {
        $tagName = $componentRenderingData->getTagName();
        $content = $componentRenderingData->getTagContent();
        $properties = $componentRenderingData->getTagProperties();

        return self::renderComponent($tagName, $content, $properties);
    }
}
