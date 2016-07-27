<?php

namespace App\AdminModule\Presenters;

use Wame\ComponentModule\Forms\ComponentForm;
use Wame\ComponentModule\Repositories\PositionRepository;


abstract class AbastractComponentPresenter extends ComponentPresenter
{		
	/** @var ComponentForm @inject */
	public $componentForm;

	/** @var PositionRepository @inject */
	public $positionRepository;
	

	/**
	 * Shop product component form
	 * 
	 * @return ComponentForm
	 */
	protected function createComponentEditForm()
	{
		$form = $this->componentForm
						->setType($this->getComponentIdentifier())
						->setId($this->id)
						->build();
		
		return $form;
	}
    
	public function renderCreate()
	{
		$this->template->siteTitle = _('Create') . ' ' . $this->getComponentName();
	}
	
	public function renderEdit()
	{
		$this->template->siteTitle = _('Edit') . ' ' . $this->getComponentName();
	}
    
    protected abstract function getComponentIdentifier();
    
    protected abstract function getComponentName();
	
}
