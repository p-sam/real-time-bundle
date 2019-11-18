<?php

namespace SP\RealTimeBundle\Command;

use SP\RealTimeBundle\Message\SenderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendMessageCommand extends Command
{
    protected static $defaultName = 'real_time:send';

    /**
     * @var SenderService
     */
    private $senderService;

    public function __construct(SenderService $senderService)
    {
        parent::__construct();

        $this->senderService = $senderService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Send a message to the specified channel')
            ->addOption('skip-presence-check', null, InputOption::VALUE_NONE, 'Skip checking presence inside the channel')
            ->addArgument('channel', InputArgument::REQUIRED, 'Channel name')
            ->addArgument('message', InputArgument::REQUIRED, 'Message to send')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $channel = $input->getArgument('channel');
        $message = $input->getArgument('message');

        if ($input->hasOption('skip-presence-check')) {
            $this->senderService->broadcastWithoutCheckingPresence($channel, $message);
        } else {
            $this->senderService->broadcast($channel, $message);
        }

        return 0;
    }
}
