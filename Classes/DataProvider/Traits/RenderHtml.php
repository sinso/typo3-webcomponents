<?php

namespace Sinso\Webcomponents\DataProvider\Traits;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

trait RenderHtml
{
    protected function renderHtml(string $content, ContentObjectRenderer $contentObjectRenderer): string
    {
        $content = trim($content);
        if (empty($content)) {
            return '';
        }

        return $contentObjectRenderer->parseFunc($content, [], '< lib.parseFunc_RTE');
    }
}
