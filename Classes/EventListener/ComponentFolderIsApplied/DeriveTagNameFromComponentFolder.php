<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\EventListener\ComponentFolderIsApplied;

use Sinso\Webcomponents\Dto\Events\ComponentFolderIsApplied;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(identifier: 'webcomponents/deriveTagNameFromComponentFolder')]
class DeriveTagNameFromComponentFolder
{
    public function __invoke(ComponentFolderIsApplied $event): void
    {
        $componentFolderPath = rtrim($event->componentFolder, '/');
        $lastFolderName = basename($componentFolderPath);
        $event->componentRenderingData->setTagName($lastFolderName);
    }
}
