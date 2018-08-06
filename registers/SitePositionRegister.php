<?php

namespace Wame\ComponentModule\Registers;

use Wame\ComponentModule\Commands\CreatePositionCommand;
use Wame\Core\Registers\BaseRegister;


class SitePositionRegister extends BaseRegister
{
    public function __construct()
    {
        parent::__construct(CreatePositionCommand::class);
    }

}
