<?php

namespace App\AdminModule\Presenters;

use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\ComponentModule\Forms\PositionForm;
use Wame\ComponentModule\Components\PositionListControl;
use Wame\ComponentModule\Components\IPositionListControlFactory;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\ComponentModule\Registers\ComponentRegister;

class PositionPresenter extends \App\AdminModule\Presenters\BasePresenter
{	
	/** @var IPositionListControlFactory @inject */
	public $IPositionListControlFactory;

	/** @var PositionRepository @inject */
	public $positionRepository; 
	
	/** @var ComponentRegister @inject */
	public $componentRegister;
	
	/** @var ComponentRepository @inject */
	public $componentRepository; 

	/** @var PositionForm @inject */
	public $positionForm; 
	
	/** @var PositionEntity */
	private $position;
	
	
	public function actionDefault()
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
	
	
	/**
	 * Position form
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
		$this->template->position = $this->position;
		$this->template->positionStatusList = $this->positionRepository->getStatusList();
		$this->template->componentStatusList = $this->componentRepository->getStatusList();
		$this->template->componentRegister = $this->componentRegister;
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
	
}