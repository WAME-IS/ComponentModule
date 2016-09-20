<?php

namespace Wame\ComponentModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Wame\Core\Entities\BaseEntity;
use Wame\Core\Entities\Columns;

/**
 * @ORM\Table(name="wame_component_in_position")
 * @ORM\Entity
 */
class ComponentInPositionEntity extends BaseEntity 
{
	use Columns\Identifier;
	use Columns\Parameters;
	use Columns\Sort;
	

	/**
     * @ORM\ManyToOne(targetEntity="ComponentEntity", fetch="EAGER")
     * @ORM\JoinColumn(name="component_id", referencedColumnName="id", nullable=false)
     */
	protected $component;

	/**
     * @ORM\ManyToOne(targetEntity="PositionEntity", fetch="EAGER")
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", nullable=false)
     */
	protected $position;
	
	
	/** get ************************************************************/

	public function getComponentInPositionName()
	{
		return $this->component->getName();
	}
	
	public function getComponent()
	{
		return $this->component;
	}
	
	public function getPosition()
	{
		return $this->position;
	}
    
	public function getComponentType()
	{
		return $this->component->getType();
	}
    
	public function getComponentStatus()
	{
		return $this->component->getStatus();
	}
	
	
	/** set ************************************************************/

	public function setComponent($component)
	{
		$this->component = $component;
		
		return $this;
	}
	
	public function setPosition($position)
	{
		$this->position = $position;
		
		return $this;
	}
	
}