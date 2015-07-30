<?php

namespace AppBundle\Command;

use AppBundle\Service\ComparisService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComparisCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('comparis:process')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Comparis process</info>');

        $service = new ComparisService($output, $this->getContainer()->get('doctrine')->getManager());

        $service->start();
        $service->checkForDelete();
    }
} 