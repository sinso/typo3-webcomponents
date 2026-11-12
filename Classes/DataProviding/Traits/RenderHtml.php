<?php

namespace Sinso\Webcomponents\DataProviding\Traits;

use Sinso\Webcomponents\Dto\Markup;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

trait RenderHtml
{
    protected function renderHtml(string $content, ContentObjectRenderer $contentObjectRenderer): Markup
    {
        $content = trim($content);
        if (empty($content)) {
            return new Markup('');
        }

        return new Markup($contentObjectRenderer->parseFunc($content, [], '< lib.parseFunc_RTE'));
    }
}
