<?php

namespace Wame\ComponentModule\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wame\ComponentModule\Registers\SiteComponentRegister;


class SiteComponentCommand extends Command
{
    /** @var SiteComponentRegister */
    private $componentRegister;


    public function injectServices(
        SiteComponentRegister $componentRegister
    ) {
        $this->componentRegister = $componentRegister;
    }


    protected function configure()
    {
        $this->setName('component:site:update')
                ->setDescription('Create site components if not exists');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->componentRegister->getAll() as $component) {
            $exec = exec('php web/index.php ' . $component->getExecCommand());

            $output->writeLn($exec);
        }
    }

}
