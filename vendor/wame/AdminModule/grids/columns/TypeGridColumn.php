<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns;

use Wame\DataGridControl\BaseGridColumn;
use Wame\ComponentModule\Registers\ComponentRegister;

class TypeGridColumn extends BaseGridColumn
{
    private $componentRegister;
    
    
    public function __construct(ComponentRegister $componentRegister)
    {
        $this->componentRegister = $componentRegister;
    }
    
	public function addColumn($grid) {
		$grid->addColumnText('type', _('Type'))
//				->setSortable()
				->setRenderer(function($item) {
					return $this->componentRegister[$item->getType()]->getTitle();
//                    return '<span class="' . $this->componentRegister[$item->getType()]->getIcon() . '" title="'. $this->componentRegister[$item->getType()]->getTitle() .'"></span>';
				});
//				->setTemplate(__DIR__ . '/grid.type.latte');
		
		return $grid;
	}
}