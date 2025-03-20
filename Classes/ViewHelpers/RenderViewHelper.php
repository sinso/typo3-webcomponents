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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class RenderViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('component', 'string', 'Class name', true);
        $this->registerArgument('inputData', 'array', 'input data', false, []);
        $this->registerArgument('contentObjectRenderer', ContentObjectRenderer::class, 'current cObj');
        $this->registerArgument('record', 'array', 'current db record', false, []);
        $this->registerArgument('table', 'string', 'current db table', false, '');
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        /** @var ComponentRenderer $componentRenderer */
        $componentRenderer = GeneralUtility::makeInstance(ComponentRenderer::class);
        if ($arguments['contentObjectRenderer'] instanceof ContentObjectRenderer) {
            $contentObjectRenderer = $arguments['contentObjectRenderer'];
        } else {
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            /** @var array<string, mixed> $record */
            $record = $arguments['record'];
            /** @var string $table */
            $table = $arguments['table'];
            $contentObjectRenderer->start($record, $table);
        }
        /** @var ContentObjectRenderer $contentObjectRenderer */
        /** @var array<string, mixed> $additionalData */
        $additionalData = $arguments['inputData'];
        $inputData = new InputData($contentObjectRenderer->data, $contentObjectRenderer->getCurrentTable(), $additionalData);
        /** @var class-string<ComponentInterface> $componentClassName */
        $componentClassName = $arguments['component'];
        try {
            $componentRenderingData = $componentRenderer->evaluateComponent($inputData, $componentClassName, $contentObjectRenderer);
        } catch (AssertionFailedException $e) {
            /** @var LogManager $logManager */
            $logManager = GeneralUtility::makeInstance(LogManager::class);
            $logger = $logManager->getLogger(__CLASS__);
            $logger->warning('Component evaluation failed', ['exception' => $e]);
            return $e->getRenderingPlaceholder();
        }

        return $componentRenderer->renderComponent($componentRenderingData, $contentObjectRenderer);
    }
}
