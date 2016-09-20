<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\ComponentInPosition;

use Nette\Utils\Html;
use Wame\DataGridControl\BaseGridItem;
use Wame\ComponentModule\Registers\ComponentRegister;

class Type extends BaseGridItem
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
		$grid->addColumnText('type', _('Type'))
				->setRenderer(function($item) {
                  return Html::el('i')
                            ->addClass('material-icons tooltipped')
                            ->addData('position', 'right')
                            ->addData('tooltip', $this->componentRegister[$item->component->getType()]->getTitle())
                            ->setText($this->componentRegister[$item->component->getType()]->getIcon());
				});
		
		return $grid;
	}
    
}