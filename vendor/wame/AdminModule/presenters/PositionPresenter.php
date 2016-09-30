<?php

namespace App\AdminModule\Presenters;

use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Forms\PositionForm;
use Wame\ComponentModule\Components\PositionListControl;
use Wame\ComponentModule\Components\IPositionListControlFactory;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\ComponentModule\Registers\ComponentRegister;
use Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\PositionGrid;
use Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\ComponentInPositionGrid;
use Wame\ComponentModule\Doctrine\Filters\ComponentStatusFilter;
use Wame\ComponentModule\Doctrine\Filters\PositionStatusFilter;


class PositionPresenter extends BasePresenter
{	
	/** @var IPositionListControlFactory @inject */
	public $IPositionListControlFactory;

	/** @var PositionRepository @inject */
	public $positionRepository; 
	
	/** @var ComponentRegister @inject */
	public $componentRegister;
	
	/** @var ComponentRepository @inject */
	public $componentRepository; 
    
    /** @var ComponentInPositionRepository @inject */
    public $componentInPositionRepository;

	/** @var PositionForm @inject */
	public $positionForm; 
    
    /** @var PositionGrid @inject */
	public $positionGrid;
    
    /** @var ComponentInPositionGrid @inject */
	public $componentInPositionGrid;
    
    /** @var ComponentStatusFilter @inject */
	public $componentStatusFilter;
    
    /** @var PositionStatusFilter @inject */
	public $positionStatusFilter;
	
	/** @var PositionEntity */
	private $position;
	
	/** @var ComponentEntity[] */
	private $components;
    
    /** @var PositionEntity[] */
    private $positions;
	
    /** actions ***************************************************************/
    
    public function actionDefault()
    {
        $this->positionStatusFilter->setEnabled(false);
        
        $this->positions = $this->positionRepository->createQueryBuilder('a');
    }
    
	public function actionShow()
	{
		if (!$this->user->isAllowed('position', 'view')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}
		
		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}
		
		$this->position = $this->positionRepository->get(['id' => $this->id]);
		
		if (!$this->position) {
			$this->flashMessage(_('This position does not exist.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
		
		if ($this->position->status == PositionRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This position is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
        
        $this->componentStatusFilter->setEnabled(false);
        
        $qb = $this->componentInPositionRepository->createQueryBuilder('a');
        $qb->join(\Wame\ComponentModule\Entities\ComponentEntity::class, 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 'a.component = c.id');
        $qb->andWhere($qb->expr()->eq('a.position', $this->id));
        $qb->andWhere($qb->expr()->neq('c.status', 0));
        
        $this->components = $qb;
	}
	
	
	public function actionCreate()
	{
		if (!$this->user->isAllowed('position', 'create')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}
	}
	
	
	public function actionUpdate()
	{
		if (!$this->user->isAllowed('position', 'update')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}
		
		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}
		
		$this->position = $this->positionRepository->get(['id' => $this->id]);
		
		if (!$this->position) {
			$this->flashMessage(_('This position does not exist.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
		
		if ($this->position->status == PositionRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This position is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
	}
	
	
	public function actionDelete()
	{
		if (!$this->user->isAllowed('position', 'delete')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}
		
		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}
		
		$this->position = $this->positionRepository->get(['id' => $this->id]);
		
		if (!$this->position) {
			$this->flashMessage(_('This position does not exist.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
		
		if ($this->position->status == PositionRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This position is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
	}
    
    
    /** handles ***************************************************************/
	
	/**
	 * Delete position
	 */
	public function handleDelete()
	{
		if (!$this->user->isAllowed('position', 'delete')) {
			$this->flashMessage(_('For this action you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');	
		}
		
		$this->positionRepository->delete(['id' => $this->id]);
		
		$this->flashMessage(_('Position has been successfully deleted.'), 'success');
		$this->redirect(':Admin:Component:', ['id' => null]);
	}

	
	/**
	 * Change position status 
	 * enabled/disabled
	 */
	public function handleChangeStatus()
	{
		if (!$this->user->isAllowed('position', 'changeStatus')) {
			$this->flashMessage(_('For this action you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');	
		}
		
		$this->positionRepository->changeStatus(['id' => $this->id]);
		
		$this->redirect('this');
	}
    
    
    public function handleSort($item_id, $prev_id, $next_id)
    {
        $this->componentInPositionRepository->move($item_id, $prev_id, $next_id);
        
        $this->flashMessage(
            "Id: $item_id, Previous id: $prev_id, Next id: $next_id",
            'success'
        );
        
        if ($this->isAjax()) {
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }
	
    
    /** renders ***************************************************************/
	
    public function renderDefault()
    {
        $this->template->siteTitle = _('Positions');
        $this->template->positions = $this->positions;
    }
    
	public function renderShow()
	{
		$this->template->siteTitle = $this->position->getTitle();
		$this->template->position = $this->position;
		$this->template->components = $this->components;
		$this->template->positionStatusList = $this->positionRepository->getStatusList();
	}
	
	
	public function renderCreate()
	{
		$this->template->setFile(__DIR__ . '/templates/Position/detail.latte');
		
		$this->template->siteTitle = _('Create position');
	}
	
	
	public function renderUpdate()
	{
		$this->template->setFile(__DIR__ . '/templates/Position/detail.latte');

		$this->template->siteTitle = _('Update position');
	}
	
	
	public function renderDelete()
	{
		$this->template->siteTitle = _('Deleting position');
	}
    
    
    /** components ************************************************************/
    
    /**
	 * Position form component
	 * 
	 * @return PositionForm
	 */
	protected function createComponentPositionForm()
	{
		$control = $this->positionForm
							->setId($this->id)
							->build();
		
		return $control;
	}
	
	
	/**
	 * Position list component
	 * 
	 * @return PositionListControl
	 */
	protected function createComponentPositionList()
	{
        $control = $this->IPositionListControlFactory->create();
        
		return $control;
	}
    
    
    /**
     * ComponentPosition grid component
     * 
     * @return PositionGrid
     */
    protected function createComponentPositionGrid()
	{
		$this->positionGrid->setDataSource($this->positions);
		$this->positionGrid->setSortable();
        
		return $this->positionGrid;
	}
    
    /**
     * ComponentInPosition grid component
     * 
     * @return ComponentInPositionGrid
     */
    protected function createComponentComponentInPositionGrid()
	{
		$this->componentInPositionGrid->setDataSource($this->components);
		$this->componentInPositionGrid->setSortable();
        $this->componentInPositionGrid->setDefaultSort(['sort' => 'ASC']);
        
		return $this->componentInPositionGrid;
	}
	
}
