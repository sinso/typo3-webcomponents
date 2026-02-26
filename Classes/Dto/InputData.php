<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

/**
 * todo: this class should be immutable and should offer ->withRecord(), ->withTableName(), and ->withAdditionalData() methods.
 */
final class InputData
{
    /**
     * @param array<string, mixed> $record
     * @param array<string, mixed> $additionalData
     */
    public function __construct(
        public array $record = [],
        public string $tableName = '',
        public array $additionalData = [],
    ) {}
}
