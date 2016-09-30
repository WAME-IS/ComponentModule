<?php

namespace App\AdminModule\Presenters;

use Nette\Utils\Html;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Repositories\ComponentRepository;
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
use Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\ComponentGrid;
use Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\CreateComponentGrid;
use Wame\MenuModule\Components\MenuControl;
use Wame\ComponentModule\Doctrine\Filters\ComponentStatusFilter;


class ComponentPresenter extends BasePresenter
{
	/** @var array */
	public $components = [];
	
	/** @var ComponentEntity */
	public $component;
	
	/** @var ComponentInPositionEntity */
	private $componentInPosition;
	
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
	public $componentItemTemplate;
    
    /** @var ComponentGrid @inject */
	public $componentGrid;
    
    /** @var CreateComponentGrid @inject */
	public $createComponentGrid;
    
    /** @var ComponentStatusFilter @inject */
	public $componentStatusFilter;
	
	
    /** actions ***************************************************************/
    
	public function actionDefault()
	{
		if (!$this->user->isAllowed('component', 'view')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');
		}

        $this->componentStatusFilter->setEnabled(false);

        $qb = $this->componentRepository->createQueryBuilder('a');
        $qb->andWhere($qb->expr()->eq('a.inList', ComponentRepository::SHOW_IN_LIST));
        
        $this->components = $qb;	
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
	 * Add component component
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
		$control->setItemTemplate($this->componentItemTemplate);
        
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
	 * Component position list component
	 * 
	 * @return ComponentPositionListControl
	 */
	protected function createComponentComponentPositionList()
	{
        $control = $this->IComponentPositionListControlFactory->create();
        
		return $control;
	}
	
	/**
	 * Component position form component
	 * 
	 * @return ComponentPositionForm
	 */
	protected function createComponentComponentPositionForm()
	{
		$form = $this->componentPositionForm->setId($this->id)->build();

		return $form;
	}
	
	/**
	 * Component add to position form component
	 * 
	 * @return ComponentAddToPositionForm
	 */
	protected function createComponentComponentAddToPositionForm()
	{
		$form = $this->componentAddToPositionForm->setId($this->id)->build();

		return $form;
	}
    
    /**
     * Component grid component
     * 
     * @param type $name
     * @return type
     */
    protected function createComponentComponentGrid()
	{
		$this->componentGrid->setDataSource($this->components);
		
		return $this->componentGrid;
	}
    
    /**
     * Component component grid
     * 
     * @param type $name
     * @return type
     */
    protected function createComponentCreateComponentGrid()
	{
//        $qb = $this->componentRepository->createQueryBuilder('a');
		$this->createComponentGrid->setDataSource($this->getComponentsArray());
		
		return $this->createComponentGrid;
	}
    
    
    /**
     * Get component array
     * 
     * @return ComponentEntity[]    components
     */
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
