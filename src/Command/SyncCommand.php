<?php

namespace SP\RealTimeBundle\Command;

use SP\RealTimeBundle\Presence\PresenceService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends Command
{
    protected static $defaultName = 'real_time:sync';

    /**
     * @var PresenceService
     */
    private $presenceService;

    public function __construct(PresenceService $presenceService)
    {
        parent::__construct();

        $this->presenceService = $presenceService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Sync ttl of channels to match their last tokens')
            ->addOption('channel', 'c', InputOption::VALUE_REQUIRED, 'Specific channel', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->presenceService->sync($input->getOption('channel'));

        return 0;
    }
}
