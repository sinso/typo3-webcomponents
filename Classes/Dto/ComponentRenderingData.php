<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

class ComponentRenderingData
{
    private ?array $additionalInputData = null;
    private ?array $contentRecord = null;
    private ?string $tagContent = null;
    private ?string $tagName = null;
    private array $tagProperties = [];

    public function getAdditionalInputData(): ?array
    {
        return $this->additionalInputData;
    }

    public function setAdditionalInputData(?array $additionalInputData): void
    {
        $this->additionalInputData = $additionalInputData;
    }

    public function getContentRecord(): ?array
    {
        return $this->contentRecord;
    }

    public function setContentRecord(?array $contentRecord): void
    {
        $this->contentRecord = $contentRecord;
    }

    public function getTagContent(): ?string
    {
        return $this->tagContent;
    }

    public function setTagContent(?string $tagContent): void
    {
        $this->tagContent = $tagContent;
    }

    public function getTagProperties(): ?array
    {
        return $this->tagProperties;
    }

    public function setTagProperty(string $key, $value): void
    {
        $this->tagProperties[$key] = $value;
    }

    public function setTagProperties(?array $tagProperties): void
    {
        $this->tagProperties = $tagProperties;
    }

    public function getTagName(): ?string
    {
        return $this->tagName;
    }

    public function setTagName(?string $tagName): void
    {
        $this->tagName = $tagName;
    }

    public function isRenderable(): bool
    {
        return $this->tagName !== null;
    }
}
