<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\Component;

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
                    if($this->componentRegister[$item->getType()]) {
                        return Html::el('i')
                            ->addClass('material-icons tooltipped')
                            ->addData('position', 'right')
                            ->addData('tooltip', $this->componentRegister[$item->getType()]->getTitle())
                            ->setText($this->componentRegister[$item->getType()]->getIcon());
                    } else {
                        return Html::el('i')
                                ->addClass('material-icons text-danger tooltipped')
                                ->addData('position', 'right')
                                ->addData('tooltip', $this->componentRegister[$item->getType()]->getTitle())
                                ->setText('help_outline');
                    }
				});
		
		return $grid;
	}
    
}