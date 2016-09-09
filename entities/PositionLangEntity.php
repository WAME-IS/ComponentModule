<?php

namespace Wame\ComponentModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Wame\Core\Entities\Columns;


/**
 * @ORM\Table(name="wame_position_lang")
 * @ORM\Entity
 */
class PositionLangEntity extends \Wame\Core\Entities\BaseLangEntity
{
    use Columns\Identifier;
    use Columns\Lang;
    use Columns\Title;
    use Columns\Description;
    use Columns\EditDate;
    use Columns\EditUser;

    
    /**
     * @ORM\ManyToOne(targetEntity="PositionEntity", inversedBy="langs")
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", nullable=false)
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

    
    public function setEntity($entity) 
    {
        $this->position = $entity;
    }

}
