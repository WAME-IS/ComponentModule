<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\Component;

use Wame\DataGridControl\BaseGridColumn;

class NameGridColumn extends BaseGridColumn
{
	public function addColumn($grid) {
		$grid->addColumnText('name', _('Name'));
		return $grid;
	}
    
}