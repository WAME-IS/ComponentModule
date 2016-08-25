<?php

namespace Wame\ComponentModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Wame\Core\Entities\Columns;
use Wame\LanguageModule\Entities\TranslatableEntity;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\Utils\Strings;

/**
 * @ORM\Table(name="wame_position")
 * @ORM\Entity
 */
class PositionEntity extends TranslatableEntity
{
	use Columns\Identifier;
	use Columns\CreateDate;
	use Columns\CreateUser;
	use Columns\Parameters;
	use Columns\Status;

	/**
     * @ORM\OneToMany(targetEntity="PositionLangEntity", mappedBy="position")
     */
    protected $langs = [];
	
	/**
     * @ORM\OneToMany(targetEntity="ComponentInPositionEntity", mappedBy="position")
     */
	protected $components;
	
	/**
	 * @ORM\Column(name="name", type="string", nullable=true)
	 */
	protected $name;
	
	
	/** get ************************************************************/
	
	public function getComponents()
	{
        return $this->components;
	}
	
	public function getName()
	{
		return $this->name;
	}
	

	/** set ************************************************************/
	
	public function setName($name)
	{
		$this->name = Strings::dashesToCamelCase($name);
		
		return $this;
	}
	
}