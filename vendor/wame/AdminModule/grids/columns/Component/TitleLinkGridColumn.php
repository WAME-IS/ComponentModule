<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\Component;

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
                    if($this->componentRegister[$item->type]) {
                        return Html::el('a')
                                ->addAttributes(['href' => $this->componentRegister[$item->type]->getLinkDetail($item)])
                                ->setText($item->title);
                    } else {
                        return $item->title;
                    }
				});
		
		return $grid;
	}
}