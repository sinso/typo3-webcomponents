<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

use Psr\Log\LoggerInterface;
use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\InputData;
use Sinso\Webcomponents\Rendering\ComponentRenderer;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

class WebcomponentContentObject extends AbstractContentObject
{
    public function __construct(
        private readonly ComponentRenderer $componentRenderer,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @param array<string, mixed> $conf
     */
    public function render($conf = []): string
    {
        $inputData = new InputData(
            $this->cObj?->data ?? [],
            $this->cObj?->getCurrentTable() ?? '',
        );

        $contentObjectRenderer = $this->getContentObjectRenderer();
        if (is_array($conf['additionalInputData.'] ?? null)) {
            // apply stdWrap to all additionalInputData properties
            foreach ($conf['additionalInputData.'] as $key => $value) {
                $key = (string)$key;
                if (!str_ends_with($key, '.')) {
                    continue;
                }
                $keyWithoutDot = substr($key, 0, -1);
                $conf['additionalInputData.'][$keyWithoutDot] = $contentObjectRenderer->stdWrapValue($keyWithoutDot, $conf['additionalInputData.']);
                unset($conf['additionalInputData.'][$key]);
            }
            $inputData->additionalData = $conf['additionalInputData.'];
        }
        $componentClassName = $contentObjectRenderer->stdWrapValue('component', $conf, null);
        if (!empty($componentClassName)) {
            try {
                /** @var class-string<ComponentInterface> $componentClassName */
                $componentRenderingData = $this->componentRenderer->evaluateComponent($inputData, $componentClassName, $contentObjectRenderer);
            } catch (AssertionFailedException $e) {
                $this->logger->warning('Component evaluation failed', ['conf' => $conf, 'data' => $inputData->record, 'exception' => $e]);
                return $e->getRenderingPlaceholder();
            }
        } else {
            $componentRenderingData = new ComponentRenderingData();
        }

        $componentRenderingData = $this->evaluateTypoScriptConfiguration($componentRenderingData, $conf);

        $markup = $this->componentRenderer->renderComponent($componentRenderingData, $contentObjectRenderer);

        // apply stdWrap
        if (is_array($conf['stdWrap.'] ?? null)) {
            $markup = $this->cObj?->stdWrap($markup, $conf['stdWrap.']) ?? $markup;
        }

        return $markup;
    }

    /**
     * @param array<string, mixed> $conf
     */
    private function evaluateTypoScriptConfiguration(ComponentRenderingData $componentRenderingData, array $conf): ComponentRenderingData
    {
        if (is_array($conf['properties.'] ?? null)) {
            foreach ($conf['properties.'] as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $componentRenderingData = $componentRenderingData->withTagProperty($key, $this->cObj?->stdWrap($value, $conf['properties.'][$key . '.'] ?? []));
            }
        }
        $tagName = $this->cObj?->stdWrapValue('tagName', $conf);
        if (is_string($tagName) && $tagName !== '') {
            return $componentRenderingData->withTagName($tagName);
        }
        return $componentRenderingData;
    }
}
