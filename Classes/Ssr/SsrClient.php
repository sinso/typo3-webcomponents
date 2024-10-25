<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Ssr;

use TYPO3\CMS\Core\Http\RequestFactory;

class SsrClient
{
    public function __construct(
        private readonly RequestFactory $requestFactory,
    ) {
    }
    public function render(string $markup): string
    {
        if (empty($_ENV['WEBCOMPONENTS_SSR_URL'])) {
            return '';
        }

        $markup = $this->requestFactory->request(
            $_ENV['WEBCOMPONENTS_SSR_URL'],
            'POST',
            [
                'headers' => [
                    'Content-Type' => 'text/html',
                ],
                'body' => $markup,
            ]
        )->getBody()->getContents();

        return $markup;
    }
}