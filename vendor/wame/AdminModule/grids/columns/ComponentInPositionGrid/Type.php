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
                    $type = $item->getComponent()->getType();

                    if ($this->componentRegister[$type]) {
                        return Html::el('small')->setText($this->componentRegister[$type]->getTitle());
                    } else {
                        return Html::el('span')
                            ->setClass('material-icons text-danger tooltipped')
                            ->setAttribute('data-tooltip', sprintf(_('Missing component %s'), $type))
                            ->setAttribute('data-position', 'right')
                            ->setText('help');
                    }
                });
		
		return $grid;
	}
    
}
