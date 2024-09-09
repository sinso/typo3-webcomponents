<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

interface TagNameSettable
{
    public function setTagName(string $tagName): void;
}
