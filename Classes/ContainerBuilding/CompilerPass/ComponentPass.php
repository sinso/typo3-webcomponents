<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContainerBuilding\CompilerPass;

use Sinso\Webcomponents\Registry\ComponentRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ComponentPass implements CompilerPassInterface
{
    /**
     * The generated TypoScript addresses components by their CType, so it must be
     * usable as a TypoScript object path.
     */
    private const CTYPE_PATTERN = '/^[A-Za-z0-9_-]+$/';

    public function __construct(
        private readonly string $tagName,
    ) {}

    public function process(ContainerBuilder $container): void
    {
        $container->findDefinition(ComponentRegistry::class)
            ->setArgument('$componentsByCType', $this->collectComponentsByCType($container));
    }

    /**
     * @return array<string, class-string>
     */
    private function collectComponentsByCType(ContainerBuilder $container): array
    {
        $componentsByCType = [];
        foreach ($container->findTaggedServiceIds($this->tagName) as $serviceName => $tags) {
            if (!class_exists($serviceName)) {
                throw new \RuntimeException(
                    'Service "' . $serviceName . '" is tagged as component for content elements, but is not a class.',
                    1786147201
                );
            }
            foreach ($tags as $tag) {
                $cType = $tag['cType'] ?? null;
                if (!is_string($cType) || preg_match(self::CTYPE_PATTERN, $cType) !== 1) {
                    throw new \RuntimeException(
                        'Component "' . $serviceName . '" is registered for an invalid CType. CTypes must '
                        . 'consist of letters, digits, underscores and dashes only.',
                        1786147202
                    );
                }
                if (isset($componentsByCType[$cType])) {
                    throw new \RuntimeException(
                        'Components "' . $componentsByCType[$cType] . '" and "' . $serviceName . '" are both '
                        . 'registered for CType "' . $cType . '". A CType can only be rendered by one component.',
                        1786147203
                    );
                }
                $componentsByCType[$cType] = $serviceName;
            }
        }
        // Keep the generated TypoScript stable, independent of the order the services were compiled in.
        ksort($componentsByCType);
        return $componentsByCType;
    }
}
