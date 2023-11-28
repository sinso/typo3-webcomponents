<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

use Sinso\Webcomponents\DataProvider\DataProviderInterface;
use Sinso\Webcomponents\Dto\WebcomponentRenderingData;
use Sinso\Webcomponents\Service\SsrClient;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class WebcomponentContentObject extends AbstractContentObject
{
    public function render($conf = []): string
    {
        $webComponentRenderingData = GeneralUtility::makeInstance(WebcomponentRenderingData::class);
        $webComponentRenderingData = $this->evaluateDataProvider($webComponentRenderingData, $conf);
        $webComponentRenderingData = $this->evaluateTypoScriptConfiguration($webComponentRenderingData, $conf);
        if (!$webComponentRenderingData->isRenderable()) {
            return '';
        }

        // render with tag builder
        $markup = $this->renderMarkup($webComponentRenderingData);

        // apply stdWrap
        $markup = $this->cObj->stdWrap($markup, $conf['stdWrap.'] ?? []);

        // apply ssr
        $markup = $this->applySsr($markup, $conf);

        return $markup;
    }

    private function evaluateDataProvider(WebcomponentRenderingData $webComponentRenderingData, array $conf): WebcomponentRenderingData
    {
        if ($conf['dataProvider']) {
            $inputData = $this->cObj->data ?? [];
            $dataProvider = GeneralUtility::makeInstance($conf['dataProvider']);
            if ($dataProvider instanceof DataProviderInterface) {
                $dataProvider->setContentObjectRenderer($this->cObj);
                $webComponentRenderingData->setTagName($dataProvider->getTagName());
                $webComponentRenderingData->setProperties($dataProvider->getProperties($inputData));
                $webComponentRenderingData->setContent($dataProvider->getContent($inputData));
            }
        }
        return $webComponentRenderingData;
    }

    private function evaluateTypoScriptConfiguration(WebcomponentRenderingData $webComponentRenderingData, array $conf): WebcomponentRenderingData
    {
        if ($conf['properties.']) {
            foreach ($conf['properties.'] as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $webComponentRenderingData->setProperty($key, $this->cObj->cObjGetSingle($value, $conf['properties.'][$key . '.']));
            }
        }
        if ($conf['tagName'] || $conf['tagName.']) {
            $webComponentRenderingData->setTagName($this->cObj->stdWrap($conf['tagName'] ?? '', $conf['tagName.'] ?? []) ?: null);
        }
        return $webComponentRenderingData;
    }

    private function renderMarkup(WebcomponentRenderingData $webComponentRenderingData): string
    {
        $tagName = $webComponentRenderingData->getTagName();
        $content = $webComponentRenderingData->getContent();
        $properties = $webComponentRenderingData->getProperties();

        // Render
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
            $tagBuilder->addAttribute($key, $value);
        }
        $tagBuilder->forceClosingTag(true);
        return $tagBuilder->render();
    }

    private function applySsr(string $markup, array $conf): string
    {
        $enableSsr = (bool)$this->cObj->stdWrap($conf['enableSsr'] ?? '', $conf['enableSsr.'] ?? []);
        if (!$enableSsr) {
            return $markup;
        }

        $ssrService = GeneralUtility::makeInstance(SsrClient::class);
        return $ssrService->render($markup);
    }
}
