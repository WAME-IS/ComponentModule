<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns;

class ComponentStatus extends \Wame\DataGridControl\Columns\Status
{
	/** {@inheritDoc} */
	public function render($grid)
	{
		$this->grid = $grid;
		
		$grid->addColumnStatus('status', _('Status'), 'component.status')
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
    
}