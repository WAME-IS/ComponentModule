<?php

namespace App\AdminModule\Presenters;

use Nette\Utils\Html;
use Wame\ComponentModule\Registers\ComponentRegister;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\MenuModule\Components\MenuControl;
use Wame\ComponentModule\Components\PositionListControl;
use Wame\ComponentModule\Components\IPositionListControlFactory;
use Wame\ComponentModule\Components\ComponentPositionListControl;
use Wame\ComponentModule\Components\IComponentPositionListControlFactory;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\ComponentModule\Forms\ComponentPositionForm;
use Wame\ComponentModule\Forms\ComponentAddToPositionForm;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Vendor\Wame\MenuModule\Components\ComponentMenu\ItemTemplate;

use Wame\DataGridControl\IDataGridControlFactory;
use Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\ComponentGrid;
use Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\CreateComponentGrid;

use Doctrine\Common\Collections\Criteria;

class ComponentPresenter extends \App\AdminModule\Presenters\BasePresenter
{
	/** @var array */
	public $components = [];
	
	/** @var ComponentEntity */
	public $component;
	
	/** @var ComponentInPositionEntity */
	private $componentInPosition;

	/** @var ComponentRegister @inject */
	public $componentRegister;
	
	/** @var ComponentRepository @inject */
	public $componentRepository;
	
	/** @var IPositionListControlFactory @inject */
	public $IPositionListControlFactory;
	
	/** @var IComponentPositionListControlFactory @inject */
	public $IComponentPositionListControlFactory;
	
	/** @var ComponentPositionForm @inject */
	public $componentPositionForm;
	
	/** @var ComponentAddToPositionForm @inject */
	public $componentAddToPositionForm;
	
	/** @var ComponentInPositionRepository @inject */
	public $componentInPositionRepository;
	
	/** @var ItemTemplate @inject */
	public $itemTemplate;
    
    /** @var IDataGridControlFactory @inject */
	public $gridControl;
    
    /** @var ComponentGrid @inject */
	public $componentGrid;
    
    /** @var CreateComponentGrid @inject */
	public $createComponentGrid;
	
	
    /** actions ***************************************************************/
    
	public function actionDefault()
	{
		if (!$this->user->isAllowed('component', 'view')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');
		}
		
		$this->components = $this->componentRepository->find(['status !=' => ComponentRepository::STATUS_REMOVE, 'inList' => ComponentRepository::SHOW_IN_LIST]);
        
        $criteriaCollection = new \Doctrine\Common\Collections\ArrayCollection($this->components);
        
        $criteria = Criteria::create()
                ->where(Criteria::expr()->in('type', $this->componentRegister->getList()));
        
        $this->components = $criteriaCollection->matching($criteria)->toArray();
    }
	
	
	public function actionPosition()
	{
		if (!$this->user->isAllowed('position', 'update')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}
		
		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}
		
		$this->componentInPosition = $this->componentInPositionRepository->get(['id' => $this->id]);
		
		if (!$this->componentInPosition) {
			$this->flashMessage(_('This component in position does not exist.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
		
		if ($this->componentInPosition->position->status == PositionRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This position is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
		
		if ($this->componentInPosition->component->status == ComponentRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This component is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
	}
	
	
	public function actionAddToPosition()
	{
		if (!$this->user->isAllowed('position', 'create')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}
		
		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}
		
		$this->component = $this->componentRepository->get(['id' => $this->id]);
		
		if (!$this->component) {
			$this->flashMessage(_('This component does not exist.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
		
		if ($this->component->status == ComponentRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This component is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
	}
	
    
    /** renders ***************************************************************/
	
	public function renderDefault()
	{
		$this->template->siteTitle = _('Components');
		$this->template->components = $this->components;
		$this->template->componentRegister = $this->componentRegister;
	}
	
	
	public function renderCreate()
	{
		$this->template->siteTitle = _('Select a type component');
	}
	
	
	public function renderDelete()
	{
		$this->template->siteTitle = _('Deleting component');
	}
	
	
	public function renderPosition()
	{
		$this->template->siteTitle = _('Edit component in position');
		$this->template->componentTitle = $this->componentInPosition->component->langs[$this->lang]->title;
		$this->template->positionTitle = $this->componentInPosition->position->langs[$this->lang]->title;
	}
	
	
	public function renderAddToPosition()
	{
		$this->template->siteTitle = _('Component add to position');
		$this->template->componentTitle = $this->component->langs[$this->lang]->title;
	}
	
	
	public function renderRemoveFromPosition()
	{
		$componentInPosition = $this->componentInPositionRepository->get(['id' => $this->id]);
		
		$this->template->siteTitle = _('Remove component in position');
		$this->template->cancelLink = $this->componentRegister->getByName($componentInPosition->component->type)->getLinkDetail($componentInPosition->component);
	}
	
    
	/** handles ***************************************************************/
    
	/**
	 * Delete component
	 */
	public function handleDelete()
	{
		if (!$this->user->isAllowed('component', 'delete')) {
			$this->flashMessage(_('For this action you do not have enough privileges.'), 'danger');
			$this->redirect('Admin:Dashboard:');	
		}
		
		$this->componentRepository->delete(['id' => $this->id]);
		
		$this->flashMessage(_('Component has been successfully deleted.'), 'success');
		$this->redirect(':Admin:Component:', ['id' => null]);
	}
	
	
	/**
	 * Remove component from position
	 */
	public function handleRemoveFromPosition()
	{
		if (!$this->user->isAllowed('component', 'delete')) {
			$this->flashMessage(_('For this action you do not have enough privileges.'), 'danger');
			$this->redirect('Admin:Dashboard:');	
		}
		
		$componentInPosition = $this->componentInPositionRepository->get(['id' => $this->id]);
		
		$this->componentInPositionRepository->remove(['id' => $this->id]);
		
		$this->flashMessage(_('Component in position has been successfully removed.'), 'success');
		
		$linkDetail = $this->componentRegister[$componentInPosition->component->type]->getLinkDetail($componentInPosition->component);
		$this->redirectUrl($linkDetail);
	}
    
    
    /** components ************************************************************/
    
    /**
	 * Add component menu
	 * 
	 * @return MenuControl
	 */
	protected function createComponentAddComponent()
	{
        $control = $this->IMenuControlFactory->create();
		$control->addProvider($this->componentRegister);

		$control->setContainerPrototype(Html::el('div')->setClass('com-componentMenu'));
		$control->setListPrototype(Html::el('div')->setClass('row'));
		$control->setItemPrototype(Html::el('div')->setClass('col-xs-6 col-sm-4 col-lg-3'));
		$control->setItemTemplate($this->itemTemplate);
        
		return $control;
	}  
	
	/**
	 * Position list
	 * 
	 * @return PositionListControl
	 */
	protected function createComponentPositionList()
	{
        $control = $this->IPositionListControlFactory->create();
        
		return $control;
	}
	
	/**
	 * Component position list
	 * 
	 * @return ComponentPositionListControl
	 */
	protected function createComponentComponentPositionList()
	{
        $control = $this->IComponentPositionListControlFactory->create();
        
		return $control;
	}
	
	/**
	 * Component position form
	 * 
	 * @return ComponentPositionForm
	 */
	protected function createComponentComponentPositionForm()
	{
		$form = $this->componentPositionForm->setId($this->id)->build();

		return $form;
	}
	
	/**
	 * Component add to position form
	 * 
	 * @return ComponentAddToPositionForm
	 */
	protected function createComponentComponentAddToPositionForm()
	{
		$form = $this->componentAddToPositionForm->setId($this->id)->build();

		return $form;
	}
    
    /**
     * Component component grid
     * 
     * @param type $name
     * @return type
     */
    protected function createComponentComponentGrid()
	{
        $components = $this->componentRepository->createQueryBuilder();
        $components
            ->andWhere($components->expr()->andX(
//                $components->expr()->neq('a.status', ComponentRepository::STATUS_REMOVE),
                $components->expr()->eq('a.inList', ComponentRepository::SHOW_IN_LIST)
            ));
        
		$grid = $this->gridControl->create();
		$grid->setDataSource($components);
        
//        $grid->setRowCallback(function($item, $tr) use ($components) {
//            $exists = $components->resetDQLParts()->andWhere($components->expr()->in('a.type', $this->componentRegister->getList()))->andWhere('a.id = :id')->setParameter('id', $item->id)->getQuery()->getOneOrNullResult();
//            $tr->addClass('super-' . count($exists));
//        });
		
		$grid->setProvider($this->componentGrid);
		
		return $grid;
	}
    
    /**
     * Component component grid
     * 
     * @param type $name
     * @return type
     */
    protected function createComponentCreateComponentGrid()
	{
		$grid = $this->gridControl->create();
		$grid->setDataSource($this->getComponentsArray());
//        $grid->setTreeView();
//        $grid->setDataSource($this->componentRepository->createQueryBuilder('a'));
		
		$grid->setProvider($this->createComponentGrid);
		
		return $grid;
	}
    
    
    private function getComponentsArray()
    {
        $components = [];
        
        foreach($this->componentRegister->getAll() as $i => $component) {
            /* @var $component \Wame\ComponentModule\Registers\IComponent */
            
            $components[] = (object) [
                'id' => $i,
                'name' => $component->getName(),
                'description' => $component->getDescription(),
                'createAction' => $component->getLinkCreate()
            ];
        }
        
        return $components;
    }

}
