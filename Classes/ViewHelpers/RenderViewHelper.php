<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ViewHelpers;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;
use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\Dto\InputData;
use Sinso\Webcomponents\Rendering\ComponentRenderer;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class RenderViewHelper extends AbstractTagBasedViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerUniversalTagAttributes();

        $this->registerArgument('component', 'string', 'Class name', true);
        $this->registerArgument('inputData', 'array', 'input data', false, []);
        $this->registerArgument('contentObjectRenderer', ContentObjectRenderer::class, 'current cObj');
        $this->registerArgument('record', 'array', 'current db record', false, []);
        $this->registerArgument('table', 'string', 'current db table', false, '');
    }

    public function render(): string
    {
        /** @var ComponentRenderer $componentRenderer */
        $componentRenderer = GeneralUtility::makeInstance(ComponentRenderer::class);
        $contentObjectRenderer = $this->getContentObjectRenderer();
        /** @var array<string, mixed> $additionalData */
        $additionalData = $this->arguments['inputData'];
        $inputData = new InputData($contentObjectRenderer->data, $contentObjectRenderer->getCurrentTable(), $additionalData);
        /** @var class-string<ComponentInterface> $componentClassName */
        $componentClassName = $this->arguments['component'];
        try {
            $componentRenderingData = $componentRenderer->evaluateComponent($inputData, $componentClassName, $contentObjectRenderer);
        } catch (AssertionFailedException $e) {
            $this->logException($e);
            return $e->getRenderingPlaceholder();
        }

        return $componentRenderer->renderComponent($componentRenderingData, $contentObjectRenderer, $this->tag);
    }

    protected function getContentObjectRenderer(): ContentObjectRenderer
    {
        if ($this->arguments['contentObjectRenderer'] instanceof ContentObjectRenderer) {
            return $this->arguments['contentObjectRenderer'];
        }

        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        /** @var array<string, mixed> $record */
        $record = $this->arguments['record'];
        /** @var string $table */
        $table = $this->arguments['table'];
        $contentObjectRenderer->start($record, $table);
        return $contentObjectRenderer;
    }

    protected function logException(\Exception $e): void
    {
        /** @var LogManager $logManager */
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $logger = $logManager->getLogger(__CLASS__);
        $logger->warning('Component evaluation failed', ['exception' => $e]);
    }
}
