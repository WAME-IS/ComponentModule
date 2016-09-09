<?php

namespace Wame\ComponentModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Wame\Core\Entities\Columns;

/**
 * @ORM\Table(name="wame_component_lang")
 * @ORM\Entity
 */
class ComponentLangEntity extends \Wame\Core\Entities\BaseLangEntity 
{
	use Columns\Identifier;
	use Columns\Lang;
	use Columns\Title;
	use Columns\Description;
	use Columns\EditDate;
	use Columns\EditUser;

	/**
     * @ORM\ManyToOne(targetEntity="ComponentEntity", inversedBy="langs")
     * @ORM\JoinColumn(name="component_id", referencedColumnName="id", nullable=false)
     */
	protected $component;

    
    public function setEntity($entity) 
    {
        $this->component = $entity; 
    }

}