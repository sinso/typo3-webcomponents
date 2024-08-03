<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\Events\ComponentFolderIsApplied;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class WebcomponentContentBlockContentObject extends AbstractWebcomponentContentObject
{
    protected function getDataProvidingStrategies(): iterable
    {
        return [
            $this->evaluateComponentFolder(...),
        ];
    }

    /**
     * @param array<string, mixed> $conf
     */
    protected function evaluateComponentFolder(ComponentRenderingData $componentRenderingData, array $conf): ComponentRenderingData
    {
        $componentFolder = $this->cObj?->stdWrapValue('componentFolder', $conf);
        if (!is_string($componentFolder)) {
            return $componentRenderingData;
        }
        $event = GeneralUtility::makeInstance(ComponentFolderIsApplied::class, $componentRenderingData, $this->cObj, $componentFolder);
        $this->eventDispatcher->dispatch($event);
        return $componentRenderingData;
    }
}
