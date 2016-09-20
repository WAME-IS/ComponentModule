<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\ComponentInPosition;

use Wame\DataGridControl\BaseGridItem;


class Delete extends BaseGridItem
{
	/** {@inheritDoc} */
	public function render($grid)
	{
		$grid->addAction('delete', '', ":Admin:Component:delete", ['id' => 'component.id'])
                ->setIcon('delete')
                ->addAttributes(['data-position' => 'left', 'data-tooltip' => _('Delete')])
                ->setClass('btn btn-xs btn-icon btn-hover-danger tooltipped ajax-modal');
		
		return $grid;
	}
    
}