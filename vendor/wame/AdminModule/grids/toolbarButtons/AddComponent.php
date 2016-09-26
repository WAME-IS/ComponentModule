<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\ToolbarButtons;

use Wame\AdminModule\Vendor\Wame\DataGridControl\ToolbarButtons\Add as AdminAdd;


class AddComponent extends AdminAdd
{
    public function __construct() 
    {
        $this->setTitle(_('Add component'));
        $this->setLink(':Admin:Component:create');
    }

}