<?php

namespace Wame\ComponentModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Wame\Core\Entities\Columns;
use Wame\Utils\Strings;

/**
 * @ORM\Table(name="wame_component")
 * @ORM\Entity
 */
class ComponentEntity extends \Wame\Core\Entities\BaseEntity 
{
	use Columns\Identifier;
	use Columns\CreateDate;
	use Columns\Parameters;
	use Columns\Status;
	

	/**
     * @ORM\OneToMany(targetEntity="ComponentLangEntity", mappedBy="component")
     */
    protected $langs;
	
	/**
	 * @ORM\Column(name="name", type="string", nullable=true)
	 */
	protected $name;
	
	/**
	 * @ORM\Column(name="type", type="string", nullable=true)
	 */
	protected $type;
	
	
	/** get ************************************************************/
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getType()
	{
		return $this->type;
	}
	

	/** set ************************************************************/
	
	public function setName($name)
	{
		$this->name = Strings::dashesToCamelCase($name);
		
		return $this;
	}
	
	public function setType($type)
	{
		$this->type = $type;
		
		return $this;
	}

}