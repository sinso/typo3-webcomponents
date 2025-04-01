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

    /**
     * @deprecated Use withTagContent() instead.
     */
    public function setTagContent(?string $tagContent): void
    {
        trigger_error('Setting tag content is deprecated. Use withTagContent() instead.', E_USER_DEPRECATED);
        $this->tagContent = $tagContent;
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

    /**
     * @deprecated Use withTagProperty() instead.
     */
    public function setTagProperty(string $key, mixed $value): void
    {
        trigger_error('Setting tag property is deprecated. Use withTagProperty() instead.', E_USER_DEPRECATED);
        $this->tagProperties[$key] = $value;
    }

    public function withTagProperty(string $key, mixed $value): self
    {
        $clonedObject = clone $this;
        $clonedObject->tagProperties[$key] = $value;
        return $clonedObject;
    }

    /**
     * @deprecated Use withTagProperties() instead.
     *
     * @param array<string, mixed> $tagProperties
     */
    public function setTagProperties(array $tagProperties): void
    {
        trigger_error('Setting tag properties is deprecated. Use withTagProperties() instead.', E_USER_DEPRECATED);
        $this->tagProperties = $tagProperties;
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

    /**
     * @deprecated Use withTagName() instead.
     */
    public function setTagName(string $tagName): void
    {
        trigger_error('Setting tag name is deprecated. Use withTagName() instead.', E_USER_DEPRECATED);
        $this->tagName = $tagName;
    }

    public function withTagName(string $tagName): self
    {
        $clonedObject = clone $this;
        $clonedObject->tagName = $tagName;
        return $clonedObject;
    }
}
