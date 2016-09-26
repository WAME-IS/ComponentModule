<?php

namespace Wame\ComponentModule\Vendor\Wame\MenuModule\Components\MenuControl\AdminMenu;

use Nette\Application\LinkGenerator;
use Wame\MenuModule\Models\Item;

interface IAdminMenuItem
{
	/** @return AdminMenuItem */
	public function create();
}


class AdminMenuItem implements \Wame\MenuModule\Models\IMenuItem
{	
    /** @var LinkGenerator */
	private $linkGenerator;
	
	
	public function __construct(
		LinkGenerator $linkGenerator
	) {
		$this->linkGenerator = $linkGenerator;
	}
    
	
	public function addItem()
	{
		$item = new Item();
		$item->setName('components');
		$item->setTitle(_('Components'));
		$item->setLink($this->linkGenerator->link('Admin:Component:', ['id' => null]));
		$item->setIcon('fa fa-puzzle-piece');
        
        $item->addNode($this->componentsDefault());
        $item->addNode($this->componentsPositions());
		
		return $item->getItem();
	}
    
    private function componentsDefault()
	{
		$item = new Item();
		$item->setName('components-components');
		$item->setTitle(_('Components'));
		$item->setLink($this->linkGenerator->link('Admin:Component:', ['id' => null]));
		
		return $item->getItem();
	}
    
    private function componentsPositions()
	{
		$item = new Item();
		$item->setName('components-positions');
		$item->setTitle(_('Positions'));
		$item->setLink($this->linkGenerator->link('Admin:Position:', ['id' => null]));
		
		return $item->getItem();
	}

}
