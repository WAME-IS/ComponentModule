<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Components;

use Wame\ComponentModule\Models\ComponentManager;

interface IAddComponentControlFactory
{
	/** @return AddComponentControl */
	public function create();	
}


class AddComponentControl extends \Nette\Application\UI\Control
{	
	/** @var array */
	private $items = [];
	
	
	public function __construct(ComponentManager $componentManager) 
	{
		parent::__construct();
		
		$this->items = $componentManager->getItems();
	}
	
	
	public function render()
	{
		$this->template->setFile(__DIR__ . '/default.latte');
		
		$this->template->items = $this->items;
		
		$this->template->render();
	}
}