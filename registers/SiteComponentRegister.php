<?php

namespace Wame\ComponentModule\Registers;

use Wame\ComponentModule\Commands\CreateComponentCommand;
use Wame\Core\Registers\BaseRegister;


class SiteComponentRegister extends BaseRegister
{
    public function __construct()
    {
        parent::__construct(CreateComponentCommand::class);
    }

}
