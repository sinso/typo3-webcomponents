<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

final class ComponentRenderingData
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

    public function withTagContent(?string $tagContent): self
    {
        $clonedObject = clone $this;
        $clonedObject->tagContent = $tagContent;
        return $clonedObject;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTagProperties(): array
    {
        return $this->tagProperties;
    }

    public function withTagProperty(string $key, mixed $value): self
    {
        $clonedObject = clone $this;
        $clonedObject->tagProperties[$key] = $value;
        return $clonedObject;
    }

    /**
     * @param array<string, mixed> $tagProperties
     */
    public function withTagProperties(array $tagProperties): self
    {
        $clonedObject = clone $this;
        $clonedObject->tagProperties = $tagProperties;
        return $clonedObject;
    }

    public function getTagName(): ?string
    {
        return $this->tagName;
    }

    public function withTagName(string $tagName): self
    {
        $clonedObject = clone $this;
        $clonedObject->tagName = $tagName;
        return $clonedObject;
    }
}
