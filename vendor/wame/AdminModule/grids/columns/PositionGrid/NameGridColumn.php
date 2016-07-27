<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\PositionGrid;

use Wame\DataGridControl\BaseGridColumn;

class NameGridColumn extends BaseGridColumn
{
	public function addColumn($grid) {
		$grid->addColumnText('name', _('Name'), 'component.name');
                
		return $grid;
	}
    
}