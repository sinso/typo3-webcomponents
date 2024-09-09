<?php

namespace Sinso\Webcomponents\Dto;

interface ComponentRenderingDataInterface
{
    public function getTagContent(): ?string;

    /**
     * @return array<string, mixed>|null
     */
    public function getTagProperties(): ?array;

    public function getTagName(): string;
}
