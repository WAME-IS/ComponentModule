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
	use Columns\Status;

	/**
     * @ORM\OneToMany(targetEntity="ArticleLangEntity", mappedBy="article")
     */
    protected $langs;
	
	/**
	 * @ORM\Column(name="publish_start_date", type="datetime", nullable=true)
	 */
	protected $publishStartDate;

	/**
	 * @ORM\Column(name="publish_end_date", type="datetime", nullable=true)
	 */
	protected $publishEndDate;

}