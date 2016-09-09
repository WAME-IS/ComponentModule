<?php

namespace Wame\ComponentModule\Entities\Columns;

trait Component
{
	/**
     * @ORM\ManyToOne(targetEntity="\Wame\ComponentModule\Entities\ComponentEntity", inversedBy="id")
     * @ORM\JoinColumn(name="component_id", referencedColumnName="id", nullable=true)
     */
    protected $component;

	
	/** get ************************************************************/

	public function getComponent()
	{
		return $this->component;
	}


	/** set ************************************************************/

	public function setComponent($component)
	{
		$this->component = $component;
		
		return $this;
	}
	
}