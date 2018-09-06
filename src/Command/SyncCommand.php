<?php

namespace SP\RealTimeBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SyncCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function configure()
    {
        $this
            ->setDescription('Sync ttl of channels to match their last tokens')
            ->addOption('channel', 'c', InputOption::VALUE_REQUIRED, 'Specific channel', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('sp_real_time.presence')->sync($input->getOption('channel'));

        return 0;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
