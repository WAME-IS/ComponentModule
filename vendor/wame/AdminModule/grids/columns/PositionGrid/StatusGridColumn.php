<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\PositionGrid;

use Wame\DataGridControl\BaseGridColumn;

class StatusGridColumn extends BaseGridColumn
{
	private $items;
	
	public function addColumn($grid) {
		$this->items = $this->getItems($grid);
		
		$grid->addColumnStatus('status', _('Status'), 'component.status')
				->setTemplate(__DIR__ . '/templates/column_status.latte')
				->addOption(1, _('Enabled'))
					->setIcon('check')
					->setClass('btn-success')
					->endOption()
				->addOption(2, _('Disabled'))
					->setIcon('close')
					->setClass('btn-danger')
					->endOption()
				->onChange[] = [$this, 'statusChange'];
		
		return $grid;
	}
	
	public function statusChange($id, $new_status)
	{
		$item = $this->items[$id];
		$item->status = $new_status;
	}
}