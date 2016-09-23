<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\ToolbarButtons;

use Wame\AdminModule\Vendor\Wame\DataGridControl\ToolbarButtons\Add as AdminAdd;


class AddPosition extends AdminAdd
{
    public function __construct() 
    {
        $this->setTitle(_('Add position'));
    }

}