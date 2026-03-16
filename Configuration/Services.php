<?php

use Sinso\Webcomponents\ContainerBuilding\Attribute\ComponentForContentElements;
use Sinso\Webcomponents\ContainerBuilding\CompilerPass\ComponentPass;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerAttributeForAutoconfiguration(
        ComponentForContentElements::class,
        static function (ChildDefinition $definition, ComponentForContentElements $attribute, \Reflector $reflector): void {
            $definition->addTag(
                ComponentForContentElements::TAG_NAME,
                ['cType' => $attribute->cType]
            );
        }
    );

    $containerBuilder->addCompilerPass(new ComponentPass(ComponentForContentElements::TAG_NAME));
};
