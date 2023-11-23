<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Service;

use TYPO3\CMS\Core\Http\RequestFactory;

class SsrClient
{
    private RequestFactory $requestFactory;

    public function __construct(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    public function render(string $markup): string
    {
        if (!$this->ssrServiceIsAvailable()) {
            return $markup;
        }
        return $this->renderWithSsrService($markup);
    }

    private function ssrServiceIsAvailable(): bool
    {
        if (empty($_ENV['TYPO3_WC_SSR_URL'])) {
            return false;
        }
        return true;
    }

    private function renderWithSsrService(string $markup): string
    {
        $response = $this->requestFactory->request($_ENV['TYPO3_WC_SSR_URL'], 'POST', [
            'body' => $markup,
            'headers' => [
                'Content-Type' => 'text/html; charset=utf-8',
            ],
        ]);
        return (string)$response->getBody();
    }
}
