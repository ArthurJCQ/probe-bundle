<?php

declare(strict_types=1);

use Arty\ProbeBundle\Model\ProbeStatusHistoryInterface;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->children()
            ->scalarNode('probe_status_history_class')
                ->isRequired()
                ->cannotBeEmpty()
                ->info('Set the probe status history class to use')
                ->validate()
                    ->ifTrue(static fn ($v): bool => null === $v || !\in_array(ProbeStatusHistoryInterface::class, class_implements($v), true))
                    ->thenInvalid(sprintf('The "probe_status_history_class" class must implement "%s".', ProbeStatusHistoryInterface::class))
                ->end()
            ->end()
            ->arrayNode('alerting')
                ->canBeEnabled()
                ->children()
                    ->scalarNode('to')->defaultNull()->end()
                    ->scalarNode('from_address')->defaultNull()->end()
                    ->scalarNode('from_name')->defaultNull()->end()
                    ->scalarNode('subject')->defaultNull()->end()
                    ->scalarNode('template')->defaultValue('@ArtyProbe/alerting/failure.html.twig')->end()
                ->end()
            ->end()
        ->end();
};
