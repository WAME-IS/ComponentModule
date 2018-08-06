<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns\ComponentInPosition;

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
				    if (isset($this->componentRegister[$item->component->type])) {
                        return Html::el('a')
                            ->addAttributes(['href' => $this->componentRegister[$item->component->type]->getLinkDetail($item->component)])
                            ->setText($item->component->title);
                    } else {
				        return $item->component->title;
                    }
				});
		
		return $grid;
	}
    
}