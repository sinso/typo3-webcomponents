<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto\Events;

use Sinso\Webcomponents\Dto\WebcomponentRenderingData;

class WebComponentWillBeRendered
{
    public function __construct(
        private readonly WebcomponentRenderingData $webcomponentRenderingData,
        private readonly array $inputData,
    ) {
    }

    public function getInputData(): array
    {
        return $this->inputData;
    }

    public function getWebcomponentRenderingData(): WebcomponentRenderingData
    {
        return $this->webcomponentRenderingData;
    }
}
