<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

class ComponentRenderingData
{
    private ?string $tagContent = null;
    private ?string $tagName = null;
    /**
     * @var array<string, mixed> $tagProperties
     */
    private array $tagProperties = [];

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
