<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

use Sinso\Webcomponents\DataProvider\DataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class WebcomponentContentObject extends AbstractContentObject
{
    public function render($conf = []): string
    {
        $tagName = null;
        $properties = null;
        $content = null;

        $inputData = $this->cObj->data ?? [];

        // Method 1: Evaluate dataProvider
        if ($conf['dataProvider']) {
            $dataProvider = GeneralUtility::makeInstance($conf['dataProvider']);
            if ($dataProvider instanceof DataProviderInterface) {
                $dataProvider->setContentObjectRenderer($this->cObj);
                $tagName = $dataProvider->getTagName();
                $properties = $dataProvider->getProperties($inputData);
                $content = $dataProvider->getContent($inputData);
            }
        }

        // Method 2: Evaluate TypoScript configuration
        if ($conf['properties.']) {
            if (!is_array($properties)) {
                $properties = [];
            }
            foreach ($conf['properties.'] as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $properties[$key] = $this->cObj->cObjGetSingle($value, $conf['properties.'][$key . '.']);
            }
        }
        if ($conf['tagName'] || $conf['tagName.']) {
            $tagName = $this->cObj->stdWrap($conf['tagName'] ?? '', $conf['tagName.'] ?? []) ?: null;
        }

        // Don't render the Web Component if tagName or properties are null
        if ($tagName === null || $properties === null) {
            return '';
        }

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
        $renderedTag = $tagBuilder->render();
        return $this->cObj->stdWrap($renderedTag, $conf['stdWrap.'] ?? []);
    }
}
