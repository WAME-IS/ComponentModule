<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\ComponentInPosition;

use Wame\DataGridControl\BaseGridColumn;
use Wame\ComponentModule\Registers\ComponentRegister;
use Nette\Utils\Html;

class TitleLinkGridColumn extends BaseGridColumn
{
    private $componentRegister;
    
    
    public function __construct(ComponentRegister $componentRegister)
    {
        $this->componentRegister = $componentRegister;
    }
    
    
	public function addColumn($grid) {
        $grid->addColumnText('title', _('Title'))
				->setRenderer(function($item) {
                    return Html::el('a')
                            ->addAttributes(['href' => $this->componentRegister[$item->component->type]->getLinkDetail($item->component)])
                            ->setText($item->component->title);
				});
		
		return $grid;
	}
}