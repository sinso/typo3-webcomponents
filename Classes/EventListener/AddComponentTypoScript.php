<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\EventListener;

use Sinso\Webcomponents\Registry\ComponentRegistry;
use Sinso\Webcomponents\TypoScript\ComponentTypoScriptGenerator;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\AfterTemplatesHaveBeenDeterminedEvent;

/**
 * Adds the TypoScript of the components registered via
 * \Sinso\Webcomponents\Attribute\ComponentForContentElement as a virtual sys_template row.
 *
 * The row is appended last, which is the only position that is evaluated after the TypoScript
 * of site sets like typo3/fluid-styled-content. Registering the TypoScript globally instead
 * (ExtensionManagementUtility::addTypoScriptSetup()) would place it before all site sets, where
 * it could not override the rendering definitions those sets ship.
 */
#[AsEventListener(identifier: 'webcomponents/add-component-typoscript')]
final class AddComponentTypoScript
{
    public function __construct(
        private readonly ComponentRegistry $componentRegistry,
        private readonly ComponentTypoScriptGenerator $typoScriptGenerator,
    ) {}

    public function __invoke(AfterTemplatesHaveBeenDeterminedEvent $event): void
    {
        $componentsByCType = $this->componentRegistry->getComponentsByCType();
        if ($componentsByCType === []) {
            return;
        }

        $templateRows = $event->getTemplateRows();
        if ($templateRows === [] && !$this->siteIsTypoScriptRoot($event->getSite())) {
            // Without any other template, the added row would become the only TypoScript
            // of a page tree that is not meant to have any.
            return;
        }

        $templateRows[] = $this->createTemplateRow($this->typoScriptGenerator->generate($componentsByCType));
        $event->setTemplateRows($templateRows);
    }

    private function siteIsTypoScriptRoot(?SiteInterface $site): bool
    {
        return $site instanceof Site && $site->isTypoScriptRoot();
    }

    /**
     * @return array<string, mixed>
     */
    private function createTemplateRow(string $setup): array
    {
        return [
            'uid' => PHP_INT_MAX,
            'pid' => 0,
            'title' => 'EXT:webcomponents components registered via #[ComponentForContentElement]',
            'root' => 0,
            'clear' => 0,
            'constants' => '',
            'config' => $setup,
            'include_static_file' => null,
            'basedOn' => null,
            'includeStaticAfterBasedOn' => 0,
            'static_file_mode' => 0,
        ];
    }
}
