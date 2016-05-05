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
	use Columns\Settings;
	

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
	
	/**
	 * @ORM\Column(name="cache", type="string", nullable=true)
	 */
	protected $cache;

	/**
	 * @ORM\Column(name="template", type="string", nullable=true)
	 */
	protected $template;
	
}