<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\Component;

use Nette\Utils\Html;
use Wame\DataGridControl\BaseGridItem;
use Wame\ComponentModule\Registers\ComponentRegister;

class TitleLink extends BaseGridItem
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