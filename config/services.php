<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Arty\ProbeBundle\Command\RunProbesCommand;
use Arty\ProbeBundle\Doctrine\ProbeManager;
use Arty\ProbeBundle\Entity\ProbeStatusHistory;
use Arty\ProbeBundle\Mailer\AlertManager;
use Arty\ProbeBundle\Mailer\ProbeFailureEmail;
use Arty\ProbeBundle\Model\AlertManagerInterface;
use Arty\ProbeBundle\Model\ProbeFailureEmailInterface;
use Arty\ProbeBundle\Model\ProbeManagerInterface;
use Arty\ProbeBundle\ProbeRunner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container, ContainerBuilder $builder) {
    $container->parameters()
        ->set('arty.probe.probe_status_history.class', ProbeStatusHistory::class)
        ->set('arty.probe.alerting.enabled', $config['alerting']['enabled'] ?? false)
        ->set('arty.probe.alert.from_address', $config['alerting']['from_address'] ?? null)
        ->set('arty.probe.alert.from_name', $config['alerting']['from_name'] ?? null)
        ->set('arty.probe.alert.to', $config['alerting']['to'] ?? null)
        ->set('arty.probe.alert.subject', $config['alerting']['subject'] ?? null)
        ->set('arty.probe.alert.template', $config['alerting']['template'] ?? null);

    $services = $container->services();

    $services->set('arty.probe.probe_manager', ProbeManager::class)
        ->args([
            new Reference(EntityManagerInterface::class),
            new Parameter('arty.probe.probe_status_history.class'),
        ]);
    $services->alias(ProbeManagerInterface::class, 'arty.probe.probe_manager');

    if ($builder->getParameter('arty.probe.alerting.enabled')) {
        $services->set('arty.probe.probe_failure_email', ProbeFailureEmail::class)
            ->args([
                new Parameter('arty.probe.alert.from_address'),
                new Parameter('arty.probe.alert.from_name'),
                new Parameter('arty.probe.alert.to'),
                new Parameter('arty.probe.alert.subject'),
                new Parameter('arty.probe.alert.template'),
            ]);
        $services->alias(ProbeFailureEmailInterface::class, 'arty.probe.probe_failure_email');

        $services->set('arty.probe.alert_manager', AlertManager::class)
            ->args([
                new Reference('mailer'),
                new Reference(ProbeFailureEmailInterface::class),
            ]);
        $services->alias(AlertManagerInterface::class, 'arty.probe.alert_manager');
    }

    $services->set('arty.probe.probe_runner', ProbeRunner::class)
        ->args([
            new AbstractArgument('probesByName'),
            new Reference(ProbeManagerInterface::class),
            new Reference(AlertManagerInterface::class, ContainerInterface::NULL_ON_INVALID_REFERENCE),
        ]);

    $services->set('arty.probe.run_probes_command', RunProbesCommand::class)
        ->args([
            new Reference(ProbeRunner::class),
        ])
        ->tag('console.command');
};
