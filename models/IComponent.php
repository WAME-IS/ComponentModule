<?php

namespace Wame\ComponentModule\Models;

use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;

interface IComponent
{
	/**
	 * Create item to Add Component
	 */
	public function addItem();
	
	/**
	 * Get link to component detail
	 * 
	 * @param ComponentEntity $componentEntity
	 */
	public function getLink($componentEntity);
	
	/**
	 * Register component
	 * 
	 * @param ComponentInPositionEntity $componentInPosition
	 */
	public function createComponent($componentInPosition);
	
}