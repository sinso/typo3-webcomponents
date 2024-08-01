<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\EventListener\ComponentFolderIsApplied;

use Praetorius\ViteAssetCollector\Service\ViteService;
use Sinso\Webcomponents\Dto\Events\ComponentFolderIsApplied;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

#[AsEventListener(identifier: 'webcomponents/addEntryPointToAssetCollector')]
class AddEntryPointToAssetCollector
{
    protected const SUPPORTED_ENTRY_POINT_FILE_NAMES = [
        'entry.ts',
        'entry.js',
    ];

    public function __construct(
        private readonly ViteService $viteService
    ) {
    }

    public function __invoke(ComponentFolderIsApplied $event): void
    {
        foreach (self::SUPPORTED_ENTRY_POINT_FILE_NAMES as $fileName) {
            $this->findAndAddEntryPoint($event->componentFolder . '/Source/' . $fileName);
        }
    }

    protected function findAndAddEntryPoint(string $entryPointPath): void
    {
        $absoluteEntryPointFile = ExtensionManagementUtility::resolvePackagePath($entryPointPath);
        if (!file_exists($absoluteEntryPointFile)) {
            return;
        }

        $this->viteService->addAssetsFromManifest(
            $this->viteService->getDefaultManifestFile(),
            $entryPointPath,
        );
    }
}