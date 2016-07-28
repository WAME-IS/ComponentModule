<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\Columns;

use Wame\DataGridControl\BaseGridColumn;

class StatusGridColumn extends BaseGridColumn
{
    private $grid;
    
    
	public function addColumn($grid)
    {
        $this->grid = $grid;
        
		$grid->addColumnStatus('status', _('Status'))
				->addOption(1, _('Enabled'))
					->setIcon('check')
					->setClass('btn-success')
					->endOption()
				->addOption(2, _('Disabled'))
					->setIcon('close')
					->setClass('btn-danger')
					->endOption()
				->onChange[] = [$this, 'statusChange'];
		
		return $grid;
	}
	
	public function statusChange($id, $new_status)
	{
        if($this->grid->getDataSource() instanceof \Doctrine\ORM\QueryBuilder) {
            $query = $this->grid->getDataSource();
            
            $item = $query->andWhere("a.id = :id")
                    ->setParameter('id', $id)
                    ->getQuery()->getSingleResult();
            
            $item->status = $new_status;
            
            if ($this->grid->presenter->isAjax()) {
                $this->grid->redrawItem($id);
            }
        }
	}
    
}