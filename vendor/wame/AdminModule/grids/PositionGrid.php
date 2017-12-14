<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Grids;

use Kdyby\Doctrine\EntityManager;
use Nette\ComponentModel\IContainer;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;


class PositionGrid extends \Wame\AdminModule\Vendor\Wame\DataGridControl\AdminDataGridControl
{
    /** @var ComponentInPositionRepository */
    private $componentInPositionRepository;


    public function __construct(
        ComponentInPositionRepository $componentInPositionRepository,
        \Kdyby\Doctrine\EntityManager $entityManager,
        \Nette\ComponentModel\IContainer $parent = null,
        $name = 'grid'
    ) {
        parent::__construct($entityManager, $parent, $name);

        $this->componentInPositionRepository = $componentInPositionRepository;
    }


    /** handles *******************************************************************************************************/

    public function handleRowSort()
    {
        $presenter = $this->getPresenter();
        $itemId = $presenter->getParameter('item_id');
        $prevId = $presenter->getParameter('prev_id');
        $nextId = $presenter->getParameter('next_id');

        $this->componentInPositionRepository->move($itemId, $prevId, $nextId);
        $this->componentInPositionRepository->resort(['position.id' => $presenter->getId()], 0);

        $presenter->getComponent('componentInPositionGrid')->redrawControl($this->getUniqueId());
    }

}
