<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\PositionGrid;

use Wame\DataGridControl\BaseGridColumn;
use Wame\ComponentModule\Registers\ComponentRegister;

use Nette\Utils\Html;

class TypeGridColumn extends BaseGridColumn
{
    private $componentRegister;
    
    
    public function __construct(ComponentRegister $componentRegister)
    {
        $this->componentRegister = $componentRegister;
    }
    
    
	public function addColumn($grid)
    {
		$grid->addColumnText('type', _('Type'))
				->setRenderer(function($item) {
                  return Html::el('span')
                          ->setClass($this->componentRegister[$item->component->getType()]->getIcon())
                          ->setTitle($this->componentRegister[$item->component->getType()]->getTitle()); //, '<span class="' .  . '" title="'. $this->componentRegister[$item->getType()]->getTitle() .'"></span>';
				});
		
		return $grid;
	}
    
}