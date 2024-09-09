<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

interface TagContentSettable
{
    public function setTagContent(?string $tagContent): void;
}
