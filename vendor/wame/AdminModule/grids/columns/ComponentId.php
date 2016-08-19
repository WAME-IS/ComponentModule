<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns;

use Wame\DataGridControl\BaseGridItem;

class ComponentId extends BaseGridItem
{
	/** {@inheritDoc} */
	public function render($grid)
	{
		$grid->addColumnText('id', _('ID'), 'component.id');
		
		return $grid;
	}
    
}