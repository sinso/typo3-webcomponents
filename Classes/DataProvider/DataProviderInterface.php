<?php

namespace Sinso\Webcomponents\DataProvider;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

interface DataProviderInterface
{
    public function getContent(array $inputData): ?string;

    public function getProperties(array $inputData): ?array;

    public function getTagName(): ?string;

    public function setContentObjectRenderer(ContentObjectRenderer $contentObjectRenderer): void;
}
