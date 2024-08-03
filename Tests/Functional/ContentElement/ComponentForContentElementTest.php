<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Tests\Functional\ContentElement;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ComponentForContentElementTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = [
        'fluid_styled_content',
    ];

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/webcomponents',
        __DIR__ . '/../Fixtures/Extensions/component_attribute_fixture',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/Fixtures/ContentElements.csv');
        $this->setUpSite();
    }

    #[Test]
    public function componentReplacesTheRenderingOfFluidStyledContent(): void
    {
        $content = $this->renderRootPage();

        self::assertStringContainsString(
            '<fixture-textmedia headline="Attributed content element"></fixture-textmedia>',
            $content
        );
        // The content element is not wrapped in the markup of fluid_styled_content anymore.
        self::assertStringNotContainsString('frame-type-textmedia', $content);
    }

    #[Test]
    public function contentElementsWithoutComponentAreStillRenderedByFluidStyledContent(): void
    {
        $content = $this->renderRootPage();

        self::assertStringContainsString('frame-type-header', $content);
        self::assertStringContainsString('Content element without component', $content);
    }

    private function renderRootPage(): string
    {
        $response = $this->executeFrontendSubRequest(new InternalRequest('https://acme.test/'));
        $response->getBody()->rewind();

        return $response->getBody()->getContents();
    }

    /**
     * The site is the TypoScript root and has no sys_template record, so all TypoScript
     * comes from the site sets and the site's own setup.typoscript.
     */
    private function setUpSite(): void
    {
        $sitePath = $this->instancePath . '/typo3conf/sites/acme/';
        GeneralUtility::mkdir_deep($sitePath);
        GeneralUtility::writeFile($sitePath . 'config.yaml', Yaml::dump([
            'rootPageId' => 1,
            'base' => 'https://acme.test/',
            'dependencies' => [
                'typo3/fluid-styled-content',
            ],
            'languages' => [
                [
                    'title' => 'English',
                    'enabled' => true,
                    'languageId' => 0,
                    'base' => '/',
                    'typo3Language' => 'default',
                    'locale' => 'en_US.UTF-8',
                    'flag' => 'us',
                ],
            ],
        ], 99, 2));
        GeneralUtility::writeFile(
            $sitePath . 'setup.typoscript',
            (string)file_get_contents(__DIR__ . '/Fixtures/setup.typoscript')
        );
    }
}
