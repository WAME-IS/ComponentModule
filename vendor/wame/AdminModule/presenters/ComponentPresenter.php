<?php

namespace App\AdminModule\Presenters;

use Nette\Utils\Html;
use Wame\ComponentModule\Models\ComponentManager;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\MenuModule\Components\MenuControl;
use Wame\PositionModule\Components\PositionListControl;
use Wame\PositionModule\Components\IPositionListControlFactory;
use Wame\ComponentModule\Vendor\Wame\MenuModule\Components\ComponentMenu\ItemTemplate;

class ComponentPresenter extends \App\AdminModule\Presenters\BasePresenter
{
	/** @var array */
	public $components = [];

	/** @var ComponentManager @inject */
	public $componentManager;
	
	/** @var ComponentRepository @inject */
	public $componentRepository;
	
	/** @var IPositionListControlFactory @inject */
	public $IPositionListControlFactory;
	
	/** @var ItemTemplate @inject */
	public $itemTemplate;
	
	
	public function actionDefault()
	{
		if (!$this->user->isAllowed('component', 'view')) {
			$this->flashMessage(_('To enter this section you do not have have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');
		}
		
		$this->components = $this->componentRepository->find(['status !=' => ComponentRepository::STATUS_REMOVE]);
	}
	
	
	/**
	 * Add component menu
	 * 
	 * @return MenuControl
	 */
	protected function createComponentAddComponent()
	{
        $control = $this->IMenuControlFactory->create();
		$control->addProvider($this->componentManager);

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

}
