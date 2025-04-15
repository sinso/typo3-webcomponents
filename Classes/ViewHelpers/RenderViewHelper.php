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

    protected function registerUniversalTagAttributes(): void
    {
        parent::registerUniversalTagAttributes();

        // the list from fluid is far from complete, so we add more possible attributes
        $this->registerTagAttribute('autocapitalize', 'string', 'Specifies how the text input should be capitalized. Allowed values: "none", "sentences", "words", "characters"');
        $this->registerTagAttribute('autocorrect', 'string', 'Specifies whether the browser should attempt to automatically correct the text input. Allowed values: "on", "off"');
        $this->registerTagAttribute('autofocus', 'boolean', 'Specifies whether the element should automatically get focus when the page loads');
        $this->registerTagAttribute('contenteditable', 'boolean', 'Specifies whether the element is editable or not');
        $this->registerTagAttribute('draggable', 'string', 'Specifies whether the element is draggable or not. Allowed values: "true", "false"');
        $this->registerTagAttribute('enterkeyhint', 'string', 'Specifies the hint for the Enter key. Allowed values: "enter", "done", "go", "next", "search", "send"');
        $this->registerTagAttribute('exportparts', 'string', 'Allows you to select and style elements existing in nested shadow trees, by exporting their part names.');
        $this->registerTagAttribute('hidden', 'string', 'Allows you to hide an element. The element will not be displayed, but it will still be present in the DOM. Allowed values: "hidden", "until-found"');
        $this->registerTagAttribute('inert', 'boolean', 'Specifies whether the element and its descendants are inert. Inert elements are not focusable and do not respond to events.');
        $this->registerTagAttribute('inputmode', 'string', 'Allowed values: "none", "text", "tel", "url", "email", "numeric", "decimal", "search"');
        $this->registerTagAttribute('itemid', 'string', 'Microdata itemid');
        $this->registerTagAttribute('itemprop', 'string', 'Microdata itemprop');
        $this->registerTagAttribute('itemref', 'string', 'Microdata itemref');
        $this->registerTagAttribute('itemscope', 'string', 'Microdata itemscope');
        $this->registerTagAttribute('itemtype', 'string', 'Microdata itemtype');
        $this->registerTagAttribute('popover', 'string', 'Allowed values: "auto", "hint", "manual"');
        $this->registerTagAttribute('slot', 'string', 'Placement of the element in the shadow DOM of the containing element');
        $this->registerTagAttribute('spellcheck', 'string', 'Specifies whether the element should be checked for spelling errors. Allowed values: "true", "false"');
        $this->registerTagAttribute('translate', 'string', 'Specifies whether the element should be translated or not. Allowed values: "yes", "no"');
        $this->registerTagAttribute('writingsuggestions', 'string', 'Specifies whether the element should show writing suggestions. Allowed values: "true", "false"');

        // and these attributes are technically not universal, but very common and possibly useful for web components
        $this->registerTagAttribute('name', 'string', 'Name of the element');
    }
}
