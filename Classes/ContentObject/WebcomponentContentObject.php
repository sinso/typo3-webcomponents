<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

use Sinso\Webcomponents\DataProvider\AssertionFailedException;
use Sinso\Webcomponents\DataProvider\Traits\RenderComponent;
use Sinso\Webcomponents\Dto\Events\WebComponentWillBeRendered;
use Sinso\Webcomponents\Dto\WebcomponentRenderingData;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

class WebcomponentContentObject extends AbstractContentObject
{
    use RenderComponent;

    public function render($conf = []): string
    {
        $webcomponentRenderingData = GeneralUtility::makeInstance(WebcomponentRenderingData::class);
        if ($this->cObj->getCurrentTable() === 'tt_content') {
            $webcomponentRenderingData->setContentRecord($this->cObj->data);
        }
        if (isset($conf['additionalInputData.'])) {
            // apply stdWrap to all additionalInputData properties
            foreach ($conf['additionalInputData.'] as $key => $value) {
                if (!str_ends_with($key, '.')) {
                    continue;
                }
                $keyWithoutDot = substr($key, 0, -1);
                $conf['additionalInputData.'][$keyWithoutDot] = $this->cObj->stdWrapValue($keyWithoutDot, $conf['additionalInputData.']);
                unset($conf['additionalInputData.'][$key]);
            }
            $webcomponentRenderingData->setAdditionalInputData($conf['additionalInputData.']);
        }
        try {
            $webcomponentRenderingData = self::evaluateDataProvider($webcomponentRenderingData, $conf['dataProvider'] ?? '', $this->cObj);
        } catch (AssertionFailedException $e) {
            return $e->getRenderingPlaceholder();
        }
        $webcomponentRenderingData = $this->evaluateTypoScriptConfiguration($webcomponentRenderingData, $conf);

        $event = GeneralUtility::makeInstance(WebComponentWillBeRendered::class, $this->cObj, $webcomponentRenderingData);
        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
        $eventDispatcher->dispatch($event);

        if (!$webcomponentRenderingData->isRenderable()) {
            return '';
        }

        // render with tag builder
        $markup = $this->renderMarkup($webcomponentRenderingData);

        // apply stdWrap
        $markup = $this->cObj->stdWrap($markup, $conf['stdWrap.'] ?? []);

        return $markup;
    }

    private function evaluateTypoScriptConfiguration(WebcomponentRenderingData $webcomponentRenderingData, array $conf): WebcomponentRenderingData
    {
        if (isset($conf['properties.'])) {
            foreach ($conf['properties.'] as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $webcomponentRenderingData->setTagProperty($key, $this->cObj->cObjGetSingle($value, $conf['properties.'][$key . '.']));
            }
        }
        if (($conf['tagName'] ?? '') || ($conf['tagName.'] ?? [])) {
            $webcomponentRenderingData->setTagName($this->cObj->stdWrap($conf['tagName'] ?? '', $conf['tagName.'] ?? []) ?: null);
        }
        return $webcomponentRenderingData;
    }

    private function renderMarkup(WebcomponentRenderingData $webcomponentRenderingData): string
    {
        $tagName = $webcomponentRenderingData->getTagName();
        $content = $webcomponentRenderingData->getTagContent();
        $properties = $webcomponentRenderingData->getTagProperties();

        return self::renderComponent($tagName, $content, $properties);
    }
}
