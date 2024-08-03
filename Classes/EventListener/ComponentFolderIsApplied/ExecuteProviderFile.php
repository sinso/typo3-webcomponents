<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\EventListener\ComponentFolderIsApplied;

use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\Dto\Events\ComponentFolderIsApplied;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

#[AsEventListener(identifier: 'webcomponents/executeProviderFile')]
class ExecuteProviderFile
{
    public function __invoke(ComponentFolderIsApplied $event): void
    {
        $absoluteComponentFolder = ExtensionManagementUtility::resolvePackagePath($event->componentFolder);
        $providerFile = $absoluteComponentFolder . '/Source/provide.php';
        if (!file_exists($providerFile)) {
            return;
        }

        $class = require $providerFile;
        $provider = new $class();
        if (!$provider instanceof ComponentInterface) {
            throw new \Exception('Provider must implement ComponentInterface', 1722526311);
        }
        $provider->setContentObjectRenderer($event->contentObjectRenderer);

        $provider->provide($event->componentRenderingData);
    }
}
