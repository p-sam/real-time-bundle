<?php

namespace SP\RealTimeBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SendMessageCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected static $defaultName = 'real_time:send';

    protected function configure()
    {
        $this
            ->setName('real_time:send')
            ->setDescription('Send a message to the specified channel')
            ->addOption('skip-presence-check', null, InputOption::VALUE_NONE, 'Skip checking presence inside the channel')
            ->addArgument('channel', InputArgument::REQUIRED, 'Channel name')
            ->addArgument('message', InputArgument::REQUIRED, 'Message to send')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channel = $input->getArgument('channel');
        $message = $input->getArgument('message');

        $senderService = $this->container->get('sp_real_time.sender');

        if ($input->hasOption('skip-presence-check')) {
            $senderService->broadcastWithoutCheckingPresence($channel, $message);
        } else {
            $senderService->broadcast($channel, $message);
        }

        return 0;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
