<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContainerBuilding\CompilerPass;

use Sinso\Webcomponents\ContainerBuilding\ComponentRegistry;
use Sinso\Webcomponents\ContainerBuilding\ComponentRegistryEntry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ComponentPass implements CompilerPassInterface
{
    public function __construct(
        private readonly string $tagName
    ) {}

    public function process(ContainerBuilder $container): void
    {
        $componentRegistryEntries = $this->collectComponentRegistryEntries($container);
        $componentRegistryDefinition = $container->findDefinition(ComponentRegistry::class);
        foreach ($componentRegistryEntries as $componentRegistryEntry) {
            $componentRegistryDefinition->addMethodCall(
                'registerComponent',
                [
                    $componentRegistryEntry->componentClassname,
                    $componentRegistryEntry->getCTypes(),
                ]
            );
        }
    }

    /**
     * @return iterable<ComponentRegistryEntry>
     */
    private function collectComponentRegistryEntries(ContainerBuilder $container): iterable
    {
        $componentClasses = [];
        foreach ($container->findTaggedServiceIds($this->tagName) as $serviceName => $tags) {
            $componentRegistryEntry = new ComponentRegistryEntry($serviceName);
            foreach ($tags as $attribute) {
                $componentRegistryEntry->addCType($attribute['cType']);
            }
            $componentClasses[] = $componentRegistryEntry;
        }
        return $componentClasses;
    }
}
