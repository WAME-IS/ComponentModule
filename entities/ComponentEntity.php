<?php

namespace Wame\ComponentModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Wame\Core\Entities\Columns;

/**
 * @ORM\Table(name="wame_component")
 * @ORM\Entity
 */
class ComponentEntity extends \Wame\Core\Entities\BaseEntity 
{
	use Columns\Identifier;
	use Columns\CreateDate;
	use Columns\Settings;
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
	
	/**
	 * @ORM\Column(name="cache", type="string", nullable=true)
	 */
	protected $cache;

	/**
	 * @ORM\Column(name="template", type="string", nullable=true)
	 */
	protected $template;
	
}