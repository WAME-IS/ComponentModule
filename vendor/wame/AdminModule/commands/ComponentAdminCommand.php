<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wame\ComponentModule\Registers\AdminComponentRegister;


class ComponentAdminCommand extends Command
{
    /** @var AdminComponentRegister */
    private $adminComponentRegister;


    public function injectServices(
        AdminComponentRegister $adminComponentRegister
    ) {
        $this->adminComponentRegister = $adminComponentRegister;
    }


    protected function configure()
    {
        $this->setName('component:admin:update')
                ->setDescription('Create admin components if not exists');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->adminComponentRegister->getAll() as $component) {
            $exec = exec('php web/index.php ' . $component->getExecCommand());

            $output->writeLn($exec);
        }
    }

}
