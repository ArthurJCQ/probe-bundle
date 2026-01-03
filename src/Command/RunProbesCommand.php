<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Command;

use Arty\ProbeBundle\ProbeRunner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'arty:probe:run', description: 'Run all probes or a specific probe')]
class RunProbesCommand extends Command
{
    public function __construct(private readonly ProbeRunner $runner)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'name',
            InputArgument::OPTIONAL,
            'The name of the probe to run. If not provided, all probes will be run.',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $probeName = $input->getArgument('name');

        if (null !== $probeName) {
            $results = [$this->runner->run($probeName)];
            $io->title(sprintf('Running probe: %s', $probeName));
        } else {
            $results = $this->runner->runAll();
            $io->title('Running all probes');
        }

        $table = new Table($output);
        $table->setHeaders(['Probe Name', 'Description', 'Status', 'Checked At']);

        foreach ($results as $probeDto) {
            $table->addRow([
                $probeDto->probeName,
                $probeDto->probeDescription,
                $probeDto->status->value,
                $probeDto->checkedAt->format('Y-m-d H:i:s'),
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
