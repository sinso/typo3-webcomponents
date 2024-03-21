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
        $webcomponentRenderingData = GeneralUtility::makeInstance(WebcomponentRenderingData::class);
        $webcomponentRenderingData = $this->evaluateDataProvider($webcomponentRenderingData, $conf['dataProvider'] ?? '', $this->cObj);
        $webcomponentRenderingData = $this->evaluateTypoScriptConfiguration($webcomponentRenderingData, $conf);

        $contentElementRecordData = $this->cObj->getCurrentTable() === 'tt_content' ? $this->cObj->data : [];
        $event = GeneralUtility::makeInstance(WebComponentWillBeRendered::class, $webcomponentRenderingData, $contentElementRecordData);
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
                $webcomponentRenderingData->setProperty($key, $this->cObj->cObjGetSingle($value, $conf['properties.'][$key . '.']));
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
        $content = $webcomponentRenderingData->getContent();
        $properties = $webcomponentRenderingData->getProperties();

        return $this->renderComponent($tagName, $content, $properties);
    }
}
