<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

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
        $webComponentRenderingData = GeneralUtility::makeInstance(WebcomponentRenderingData::class);
        $webComponentRenderingData = $this->evaluateDataProvider($webComponentRenderingData, $conf['dataProvider'] ?? '', $this->cObj);
        $webComponentRenderingData = $this->evaluateTypoScriptConfiguration($webComponentRenderingData, $conf);

        $contentElementRecordData = $this->cObj->getCurrentTable() === 'tt_content' ? $this->cObj->data : [];
        $event = GeneralUtility::makeInstance(WebComponentWillBeRendered::class, $webComponentRenderingData, $contentElementRecordData);
        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
        $eventDispatcher->dispatch($event);

        if (!$webComponentRenderingData->isRenderable()) {
            return '';
        }

        // render with tag builder
        $markup = $this->renderMarkup($webComponentRenderingData);

        // apply stdWrap
        $markup = $this->cObj->stdWrap($markup, $conf['stdWrap.'] ?? []);

        return $markup;
    }

    private function evaluateTypoScriptConfiguration(WebcomponentRenderingData $webComponentRenderingData, array $conf): WebcomponentRenderingData
    {
        if (isset($conf['properties.'])) {
            foreach ($conf['properties.'] as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $webComponentRenderingData->setProperty($key, $this->cObj->cObjGetSingle($value, $conf['properties.'][$key . '.']));
            }
        }
        if (($conf['tagName'] ?? '') || ($conf['tagName.'] ?? [])) {
            $webComponentRenderingData->setTagName($this->cObj->stdWrap($conf['tagName'] ?? '', $conf['tagName.'] ?? []) ?: null);
        }
        return $webComponentRenderingData;
    }

    private function renderMarkup(WebcomponentRenderingData $webComponentRenderingData): string
    {
        $tagName = $webComponentRenderingData->getTagName();
        $content = $webComponentRenderingData->getContent();
        $properties = $webComponentRenderingData->getProperties();

        return $this->renderComponent($tagName, $content, $properties);
    }
}
