<?php

namespace Wame\ComponentModule\Models;

use Wame\MenuModule\Models\ItemSorter;

class ComponentManager
{	
	/** @var array */
	private $components = [];
	
	/** @var ItemSorter */
	private $itemSorter;
	
	
	public function __construct(ItemSorter $itemSorter) 
	{
		$this->itemSorter = $itemSorter;
	}
	
	
	/**
	 * Add component
	 * 
	 * @param object $component
	 * @return \Wame\ComponentModule\Models\ComponentManager
	 */
	public function addComponent($component)
	{
		$name = $this->getClassName($component);

		$this->components[$name] = $component;
		
		return $this;
	}
	

    /**
     * Get items from services
     * 
     * @return array
     */
    public function getItems()
    {
        return $this->itemSorter->sort($this->components);
    }
	
	
	/**
	 * Get class name from namespace
	 * 
	 * @param string $namespace
	 * @return string
	 */
	public function getClassName($namespace)
	{
		$reflect = new \ReflectionClass($namespace);
		
		return $reflect->getShortName();
	}

}
