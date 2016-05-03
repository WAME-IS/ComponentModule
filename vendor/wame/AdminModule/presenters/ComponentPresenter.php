<?php

namespace App\AdminModule\Presenters;

use Nette\Utils\Html;
use Wame\ComponentModule\Vendor\Wame\AdminModule\Components\IAddComponentControlFactory;
use Wame\ComponentModule\Models\ComponentManager;
//use Wame\MenuModule\Components\IMenuControlFactory;

class ComponentPresenter extends \App\AdminModule\Presenters\BasePresenter
{
	/** @var ComponentManager @inject */
	public $componentManager;
	
	/** @var IAddComponentControlFactory @inject */
	public $IAddComponentControlFactory;
	
//	/** @var IMenuControlFactory @inject */
//	public $IMenuControlFactory;
	
	
//	protected function createComponentAddComponent()
//	{
//		$control = $this->IAddComponentControlFactory->create();
//		
//		return $control;
//	}
	
	
	protected function createComponentAddComponent()
	{
        $control = $this->IMenuControlFactory->create();
		$control->addProvider($this->componentManager);

		$control->setContainerPrototype(Html::el('div')->setClass('com-componentMenu'));
		$control->setListPrototype(Html::el('ul')->setClass('list-group'));
		$control->setItemPrototype(Html::el('li')->setClass('list-group-item'));
        
		return $control;
	}  
	
	
	public function renderDefault()
	{
		$this->template->siteTitle = _('Components');
	}

}
