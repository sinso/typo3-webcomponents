<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

final class ComponentRenderingData
{
    private ?Markup $tagContent = null;
    private ?string $tagName = null;
    /**
     * @var array<string, mixed> $tagProperties
     */
    private array $tagProperties = [];

    public function getTagContent(): ?Markup
    {
        return $this->tagContent;
    }

    /**
     * A string is plain text and gets escaped. Pass Markup to write HTML.
     */
    public function withTagContent(string|Markup|null $tagContent): self
    {
        $clonedObject = clone $this;
        $clonedObject->tagContent = is_string($tagContent) ? Markup::join($tagContent) : $tagContent;
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
