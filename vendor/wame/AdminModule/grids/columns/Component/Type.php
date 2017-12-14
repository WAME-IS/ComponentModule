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
                    if ($this->componentRegister[$item->getType()]) {
                        return Html::el('small')->setText($this->componentRegister[$item->getType()]->getTitle());
                    } else {
                        return Html::el('span')
                            ->setClass('material-icons text-danger tooltipped')
                            ->setAttribute('data-tooltip', sprintf(_('Missing component %s'), $item->getType()))
                            ->setAttribute('data-position', 'right')
                            ->setText('help');
                    }
                })
                ->setFilterSelect(array_replace(['' => '- ' . _('Select from list') . ' -'], $this->componentRegister->getComponentList()), 'type');

        return $grid;
    }
    
}
