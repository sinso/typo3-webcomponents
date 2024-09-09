<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

class InputData
{
    /**
     * @param array<string, string|integer> $record
     * @param array<string, mixed> $additionalData
     */
    public function __construct(
        public array $record = [],
        public string $tableName = '',
        public array $additionalData = [],
    ) {}
}
