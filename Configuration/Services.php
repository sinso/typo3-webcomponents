<?php

use Sinso\Webcomponents\Attribute\ComponentForContentElement;
use Sinso\Webcomponents\ContainerBuilding\CompilerPass\ComponentPass;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerAttributeForAutoconfiguration(
        ComponentForContentElement::class,
        static function (ChildDefinition $definition, ComponentForContentElement $attribute, \Reflector $reflector): void {
            $definition->addTag(
                ComponentForContentElement::TAG_NAME,
                ['cType' => $attribute->cType]
            );
        }
    );

    $containerBuilder->addCompilerPass(new ComponentPass(ComponentForContentElement::TAG_NAME));
};
