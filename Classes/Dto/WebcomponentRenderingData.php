<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

class WebcomponentRenderingData
{
    private ?string $content = null;
    private array $properties = [];
    private ?string $tagName;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function setProperty(string $key, $value): void
    {
        $this->properties[$key] = $value;
    }

    public function setProperties(?array $properties): void
    {
        $this->properties = $properties;
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
