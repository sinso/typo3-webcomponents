<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContainerBuilding\EventListener;

use Sinso\Webcomponents\ContainerBuilding\ComponentRegistry;
use Sinso\Webcomponents\ContainerBuilding\ComponentRegistryEntry;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

#[AsEventListener(identifier: 'webcomponents/addComponentsToTypoScript')]
class AddComponentsToTypoScript
{
    public function __invoke(BootCompletedEvent $event): void
    {
        ExtensionManagementUtility::addTypoScriptSetup($this->generateTypoScript());
    }

    public function __construct(
        private readonly ComponentRegistry $componentRegistry,
    ) {}

    private function generateTypoScript(): string
    {
        $typoScript = '';
        foreach ($this->componentRegistry->getComponentRegistryEntries() as $entry) {
            $typoScript .= $this->generateTypoScriptForComponent($entry);
        }
        return $typoScript;
    }

    private function generateTypoScriptForComponent(ComponentRegistryEntry $entry): string
    {
        $typoScript = '';
        foreach ($entry->getCTypes() as $cType) {
            $typoScript .= $this->generateTypoScriptForCType($entry, $cType);
        }
        return $typoScript;
    }

    private function generateTypoScriptForCType(ComponentRegistryEntry $entry, string $cType): string
    {
        return <<<TYPOSCRIPT
tt_content.{$cType} >
tt_content.{$cType} = WEBCOMPONENT
tt_content.{$cType}.component = {$entry->componentClassname}
TYPOSCRIPT;
    }
}
