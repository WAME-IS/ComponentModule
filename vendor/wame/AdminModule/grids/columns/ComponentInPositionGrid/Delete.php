<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\ComponentInPosition;

use Wame\ComponentModule\Registers\ComponentRegister;
use Wame\DataGridControl\BaseGridItem;

class Delete extends BaseGridItem
{
    /** @var ComponentRegister */
    private $componentRegister;
    
    
    public function __construct(ComponentRegister $componentRegister)
    {
        $this->componentRegister = $componentRegister;
    }
    
    
	/** {@inheritDoc} */
	public function render($grid)
	{
		$grid->addAction('delete', '', ":Admin:Component:delete", ['id' => 'component.id'])
			->setIcon('trash')
			->setTitle(_('Delete'))
			->setClass('btn btn-xs btn-danger ajax-modal');
		
		return $grid;
	}
    
}