<?php

namespace Wame\ComponentModule\Entities\Columns;

trait Position
{
	/**
     * @ORM\ManyToOne(targetEntity="\Wame\ComponentModule\Entities\PositionEntity", inversedBy="id")
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", nullable=true)
     */
    protected $position;

	
	/** get ************************************************************/

	public function getPosition()
	{
		return $this->position;
	}


	/** set ************************************************************/

	public function setPosition($position)
	{
		$this->position = $position;
		
		return $this;
	}
	
}