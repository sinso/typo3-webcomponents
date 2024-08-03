<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Tests\Unit\TypoScript;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sinso\Webcomponents\TypoScript\ComponentTypoScriptGenerator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ComponentTypoScriptGenerator::class)]
class ComponentTypoScriptGeneratorTest extends UnitTestCase
{
    private ComponentTypoScriptGenerator $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new ComponentTypoScriptGenerator();
    }

    #[Test]
    public function generatesNothingWithoutRegisteredComponents(): void
    {
        self::assertSame('', $this->subject->generate([]));
    }

    #[Test]
    public function resetsAndAssignsTheRenderingOfARegisteredContentElement(): void
    {
        $expected = <<<TYPOSCRIPT
        tt_content.image >
        tt_content.image = WEBCOMPONENT
        tt_content.image.component = Acme\\Components\\Image

        TYPOSCRIPT;

        self::assertSame($expected, $this->subject->generate(['image' => 'Acme\\Components\\Image']));
    }

    #[Test]
    public function generatesTypoScriptForEveryRegisteredContentElement(): void
    {
        $expected = <<<TYPOSCRIPT
        tt_content.image >
        tt_content.image = WEBCOMPONENT
        tt_content.image.component = Acme\\Components\\Image
        tt_content.tx_acme_teaser >
        tt_content.tx_acme_teaser = WEBCOMPONENT
        tt_content.tx_acme_teaser.component = Acme\\Components\\Teaser

        TYPOSCRIPT;

        self::assertSame($expected, $this->subject->generate([
            'image' => 'Acme\\Components\\Image',
            'tx_acme_teaser' => 'Acme\\Components\\Teaser',
        ]));
    }
}
