<?php

declare(strict_types=1);

namespace Arty\ProbeBundle;

use Arty\ProbeBundle\DependencyInjection\ProbeCompilerPass;
use Arty\ProbeBundle\Entity\ProbeStatusHistory;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ArtyProbeBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ProbeCompilerPass());
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import(__DIR__ . '/../config/definition.php');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()
            ->set('arty.probe.probe_status_history.class', ProbeStatusHistory::class)
            ->set('arty.probe.alerting.enabled', $config['alerting']['enabled'] ?? false)
            ->set('arty.probe.alerting.from_address', $config['alerting']['from_address'] ?? null)
            ->set('arty.probe.alerting.from_name', $config['alerting']['from_name'] ?? null)
            ->set('arty.probe.alerting.to', $config['alerting']['to'] ?? null)
            ->set('arty.probe.alerting.subject', $config['alerting']['subject'] ?? null)
            ->set('arty.probe.alerting.template', $config['alerting']['template'] ?? null);

        $container->import(__DIR__ . '/../config/services.php');
    }
}
