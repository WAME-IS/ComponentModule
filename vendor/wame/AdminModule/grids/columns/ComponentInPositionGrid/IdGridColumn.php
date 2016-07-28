<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\ComponentInPositionGrid;

use Wame\DataGridControl\BaseGridColumn;

class IdGridColumn extends BaseGridColumn
{
	public function addColumn($grid)
    {
		$grid->addColumnText('id', _('ID'), 'component.id');
		
		return $grid;
	}
    
}