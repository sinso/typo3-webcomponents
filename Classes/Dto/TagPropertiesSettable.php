<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

interface TagPropertiesSettable
{
    public function setTagProperty(string $key, mixed $value): void;

    /**
     * @param array<string, mixed> $tagProperties
     */
    public function setTagProperties(array $tagProperties): void;
}
