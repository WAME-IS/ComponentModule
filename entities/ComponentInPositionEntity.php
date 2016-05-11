<?php

namespace Wame\ComponentModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Wame\Core\Entities\Columns;

/**
 * @ORM\Table(name="wame_component_in_position")
 * @ORM\Entity
 */
class ComponentInPositionEntity extends \Wame\Core\Entities\BaseEntity 
{
	use Columns\Identifier;
	use Columns\Parameters;
	

	/**
     * @ORM\ManyToOne(targetEntity="ComponentEntity", inversedBy="component")
     * @ORM\JoinColumn(name="component_id", referencedColumnName="id", nullable=false)
     */
	protected $component;

	/**
     * @ORM\ManyToOne(targetEntity="\Wame\PositionModule\Entities\PositionEntity")
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
		return $this->position->name . '_' . $this->component->type . '_' . $this->component->name . '_' . $this->component->id;
	}
	
	public function getSort()
	{
		return $this->sort;
	}
	
	
	/** set ************************************************************/

	public function setSort($sort)
	{
		$this->sort = $sort;
		
		return $this;
	}
	
}