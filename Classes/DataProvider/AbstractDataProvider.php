<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProvider;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

abstract class AbstractDataProvider implements DataProviderInterface
{
    protected ContentObjectRenderer $contentObjectRenderer;

    protected array $inputData;

    public function getContent(): ?string
    {
        return null;
    }

    public function setContentObjectRenderer(ContentObjectRenderer $contentObjectRenderer): void
    {
        $this->contentObjectRenderer = $contentObjectRenderer;
    }

    public function setInputData(array $inputData): void
    {
        $this->inputData = $inputData;
    }
}
