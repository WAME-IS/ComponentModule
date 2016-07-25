<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\CreateComponent;

class CreateGridAction extends BaseGridColumn
{
	public function addColumn($grid)
	{
		$grid->addAction('edit', '', ":{$grid->parent->presenterName}:edit")
			->setIcon('edit')
			->setTitle(_('Edit'))
			->setClass('btn btn-xs btn-info');
		
		return $grid;
	}
}