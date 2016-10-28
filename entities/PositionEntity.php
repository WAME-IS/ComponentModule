<?php

namespace Wame\ComponentModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Wame\Core\Entities\Columns;
use Wame\LanguageModule\Entities\TranslatableEntity;
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
     * @ORM\OneToMany(targetEntity="PositionLangEntity", mappedBy="position", cascade={"persist"})
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

	/**
	 * @ORM\Column(name="in_list", type="integer", nullable=false)
	 */
	protected $inList = 1;


	/** get ************************************************************/

	public function getComponents()
	{
        return $this->components;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getInList()
	{
		return $this->inList;
	}


	/** set ************************************************************/

	public function setName($name)
	{
		$this->name = Strings::dashesToCamelCase($name);

		return $this;
	}

	public function setInList($inList)
	{
		$this->inList = $inList;

		return $this;
	}

}