<?php

namespace Wame\ComponentModule\Commands;

use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wame\ComponentModule\Registers\SitePositionRegister;


class SitePositionCommand extends Command
{
    /** @var Container */
    private $container;

    /** @var SitePositionRegister */
    private $positionRegister;


    public function injectServices(
        Container $container,
        SitePositionRegister $positionRegister
    ) {
        $this->container = $container;
        $this->positionRegister = $positionRegister;
    }


    protected function configure()
    {
        $this->setName('position:site:update')
                ->setDescription('Create site positions if not exists');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->positionRegister->getAll() as $component) {
            $exec = exec('php web/index.php ' . $component->getExecCommand());

            $output->writeLn($exec);
        }
    }

}
