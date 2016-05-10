<?php

namespace Wame\ComponentModule\Models;

use Wame\MenuModule\Models\ItemSorter;

class ComponentManager
{	
	/** @var array */
	public $components = [];
	
	/** @var array */
	private $removeComponent = [];
	
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
	 * Add component to remove list
	 * 
	 * @param object $component
	 * @return \Wame\ComponentModule\Models\ComponentManager
	 */
	public function removeComponent($component)
	{
		$name = $this->getClassName($component);
		
		$this->removeComponent[$name] = $name;
		
		return $this;
	}
	
	
	private function removeComponents()
	{
		$components = $this->components;
		
		foreach ($this->removeComponent as $component) {
			if (array_key_exists($component, $components)) {
				unset($components[$component]);
			}
		}
		
		return $components;
	}
	

    /**
     * Get items from services
     * 
     * @return array
     */
    public function getItems()
    {
		$components = $this->removeComponents();
				
        return $this->itemSorter->sort($components);
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
