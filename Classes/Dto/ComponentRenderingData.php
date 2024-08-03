<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

class ComponentRenderingData
{
    /**
     * @var array<string, mixed>|null $additionalInputData
     */
    private ?array $additionalInputData = null;
    /**
     * @var array<string, string|integer>|null $contentRecord
     */
    private ?array $contentRecord = null;
    private ?string $tagContent = null;
    private ?string $tagName = null;
    /**
     * @var array<string, mixed> $tagProperties
     */
    private array $tagProperties = [];

    /**
     * @return array<string, mixed>|null
     */
    public function getAdditionalInputData(): ?array
    {
        return $this->additionalInputData;
    }

    /**
     * @param array<string, mixed>|null $additionalInputData
     */
    public function setAdditionalInputData(?array $additionalInputData): void
    {
        $this->additionalInputData = $additionalInputData;
    }

    /**
     * @return array<string, string|integer>|null
     */
    public function getContentRecord(): ?array
    {
        return $this->contentRecord;
    }

    /**
     * @param array<string, string|integer>|null $contentRecord
     */
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

    /**
     * @return array<string, mixed>
     */
    public function getTagProperties(): array
    {
        return $this->tagProperties;
    }

    public function setTagProperty(string $key, mixed $value): void
    {
        $this->tagProperties[$key] = $value;
    }

    /**
     * @param array<string, mixed> $tagProperties
     */
    public function setTagProperties(array $tagProperties): void
    {
        $this->tagProperties = $tagProperties;
    }

    public function getTagName(): ?string
    {
        return $this->tagName;
    }

    public function setTagName(string $tagName): void
    {
        $this->tagName = $tagName;
    }
}
