<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Tests\Unit\ContainerBuilding\CompilerPass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sinso\Webcomponents\ContainerBuilding\CompilerPass\ComponentPass;
use Sinso\Webcomponents\Registry\ComponentRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ComponentPass::class)]
class ComponentPassTest extends UnitTestCase
{
    private const TAG_NAME = 'webcomponents.componentForContentElement';

    private ComponentPass $subject;
    private ContainerBuilder $containerBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new ComponentPass(self::TAG_NAME);
        $this->containerBuilder = new ContainerBuilder();
        $this->containerBuilder->register(ComponentRegistry::class, ComponentRegistry::class);
    }

    #[Test]
    public function registersNoComponentsIfNoneAreTagged(): void
    {
        $this->subject->process($this->containerBuilder);

        self::assertSame([], $this->getRegisteredComponents());
    }

    #[Test]
    public function registersOneComponentPerTaggedCType(): void
    {
        $this->registerComponent(\ArrayObject::class, 'tx_acme_teaser');
        $this->registerComponent(\stdClass::class, 'image', 'textmedia');

        $this->subject->process($this->containerBuilder);

        self::assertSame(
            [
                'image' => \stdClass::class,
                'textmedia' => \stdClass::class,
                'tx_acme_teaser' => \ArrayObject::class,
            ],
            $this->getRegisteredComponents()
        );
    }

    #[Test]
    public function throwsIfTwoComponentsAreRegisteredForTheSameCType(): void
    {
        $this->registerComponent(\ArrayObject::class, 'image');
        $this->registerComponent(\stdClass::class, 'image');

        $this->expectExceptionCode(1786147203);

        $this->subject->process($this->containerBuilder);
    }

    #[Test]
    public function throwsIfACTypeIsNoValidTypoScriptObjectPath(): void
    {
        $this->registerComponent(\stdClass::class, 'acme.image');

        $this->expectExceptionCode(1786147202);

        $this->subject->process($this->containerBuilder);
    }

    private function registerComponent(string $className, string ...$cTypes): void
    {
        $definition = $this->containerBuilder->register($className, $className);
        foreach ($cTypes as $cType) {
            $definition->addTag(self::TAG_NAME, ['cType' => $cType]);
        }
    }

    /**
     * @return array<string, class-string>
     */
    private function getRegisteredComponents(): array
    {
        return $this->containerBuilder->findDefinition(ComponentRegistry::class)->getArgument('$componentsByCType');
    }
}
