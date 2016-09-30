<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\ComponentInPosition;

use Wame\DataGridControl\BaseGridItem;


class Sort extends BaseGridItem
{
	/** {@inheritDoc} */
	public function render($grid)
	{
		$grid->addColumnNumber('sort', _('Sort'));
		
		return $grid;
	}
    
}