<?php

declare(strict_types=1);

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->children()
            ->arrayNode('alerting')
                ->canBeEnabled()
                ->children()
                    ->scalarNode('to')->defaultNull()->end()
                    ->scalarNode('from_address')->defaultNull()->end()
                    ->scalarNode('from_name')->defaultNull()->end()
                    ->scalarNode('subject')->defaultNull()->end()
                    ->scalarNode('template')->defaultValue('@ArtyProbeBundle/alerting/failure.html.twig')->end()
                ->end()
            ->end()
        ->end();
};
