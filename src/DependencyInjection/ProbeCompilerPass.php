<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\DependencyInjection;

use Arty\ProbeBundle\Attribute\Probe as ProbeAttribute;
use Arty\ProbeBundle\Model\ProbeInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProbeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $probeRunnerDef = $container->getDefinition('arty.probe.probe_runner');
        $probeRefs = [];

        $taggedServices = $container->findTaggedServiceIds('app.probe');

        foreach (array_keys($taggedServices) as $id) {
            $definition = $container->getDefinition($id);

            /** @var class-string<ProbeInterface> $class */
            $class = $definition->getClass();

            if (!$class) {
                continue;
            }

            $reflection = new \ReflectionClass($class);
            $attr = $reflection->getAttributes(ProbeAttribute::class);

            if (!$attr) {
                continue;
            }

            /** @var ProbeAttribute $probeAttr */
            $probeAttr = $attr[0]->newInstance();
            $name = $probeAttr->name;

            $probeRefs[$name] = [
                'probeInstance' => new Reference($id),
                'name' => $probeAttr->name,
                'successThreshold' => $probeAttr->successThreshold,
                'warningThreshold' => $probeAttr->warningThreshold,
                'failureThreshold' => $probeAttr->failureThreshold,
                'notify' => $probeAttr->notify,
                'description' => $probeAttr->description ?? null,
            ];
        }

        $probeRunnerDef->setArgument('$probesByName', $probeRefs);
    }
}
