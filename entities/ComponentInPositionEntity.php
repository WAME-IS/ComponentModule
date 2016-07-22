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

	/**
	 * @ORM\Column(name="sort", type="integer", nullable=false)
	 */
	protected $sort;
	
	
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
	
	public function getSort()
	{
		return $this->sort;
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

	public function setSort($sort)
	{
		$this->sort = $sort;
		
		return $this;
	}
	
}