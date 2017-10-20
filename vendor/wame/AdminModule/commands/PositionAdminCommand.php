<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wame\ComponentModule\Registers\AdminPositionRegister;


class PositionAdminCommand extends Command
{
    /** @var AdminPositionRegister */
    private $adminPositionRegister;


    public function injectServices(
        AdminPositionRegister $adminPositionRegister
    ) {
        $this->adminPositionRegister = $adminPositionRegister;
    }


    protected function configure()
    {
        $this->setName('position:admin:update')
                ->setDescription('Create admin positions if not exists');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->adminPositionRegister->getAll() as $component) {
            $exec = exec('php web/index.php ' . $component->getExecCommand());

            $output->writeLn($exec);
        }
    }

}
