<?php

declare(strict_types=1);

use Arty\ProbeBundle\Model\AbstractProbeStatusHistory;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->children()
            ->scalarNode('probe_status_history_class')
                ->isRequired()
                ->cannotBeEmpty()
                ->info('Set the probe status history class to use')
                ->validate()
                    ->ifTrue(static fn ($v): bool => null === $v || !\in_array(AbstractProbeStatusHistory::class, class_parents($v), true))
                    ->thenInvalid(sprintf('The "probe_status_history_class" class must extends "%s".', AbstractProbeStatusHistory::class))
                ->end()
            ->end()
            ->arrayNode('alerting')
                ->canBeEnabled()
                ->children()
                    ->enumNode('channel')
                        ->values(['email', 'chat'])
                        ->defaultValue('email')
                        ->info('The notification channel to use (email or chat)')
                    ->end()
                    ->scalarNode('to')
                        ->defaultNull()
                        ->info('Recipient email address (required when channel is email)')
                    ->end()
                ->end()
                ->validate()
                    ->ifTrue(static fn (array $v): bool => $v['enabled'] && 'email' === $v['channel'] && empty($v['to']))
                    ->thenInvalid('The "to" option is required when using the email channel.')
                ->end()
            ->end()
        ->end();
};
