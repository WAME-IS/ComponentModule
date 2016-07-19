<?php

namespace Wame\ComponentModule\Registers;

use Wame\ComponentModule\Entities\ComponentEntity;

interface IComponent
{
	/**
	 * Create item to Add Component
	 */
	public function addItem();
	
	/**
	 * Component name
	 */
	public function getName();
	
	/**
	 * Component title
	 */
	public function getTitle();
	
	/**
	 * Component description
	 */
	public function getDescription();
	
	/**
	 * Component icon
	 */
	public function getIcon();
	
	/**
	 * Link to create component [admin]
	 */
	public function getLinkCreate();
	
	/**
	 * Get link to component detail [admin]
	 * 
	 * @param ComponentEntity $componentEntity
	 */
	public function getLinkDetail($componentEntity);
	
	/**
	 * Create new instance of component
	 */
	public function createComponent();
	
}