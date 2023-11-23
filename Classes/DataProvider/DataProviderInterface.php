<?php

namespace Sinso\Webcomponents\DataProvider;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

interface DataProviderInterface
{
    public function getContent(): ?string;

    public function getProperties(): ?array;

    public function getTagName(): ?string;

    public function setInputData(array $inputData): void;

    public function setContentObjectRenderer(ContentObjectRenderer $contentObjectRenderer): void;
}
