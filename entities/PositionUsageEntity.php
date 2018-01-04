<?php

namespace Wame\ComponentModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Wame\Core\Entities\Columns;


/**
 * @ORM\Table(name="wame_position_usage")
 * @ORM\Entity
 */
class PositionUsageEntity extends \Wame\Core\Entities\BaseEntity
{
    use Columns\Identifier;


    /**
     * @ORM\ManyToOne(targetEntity="PositionEntity", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", nullable=false)
     */
    protected $position;

    /**
     * @ORM\Column(name="presenter", type="string", length=150)
     */
    protected $presenter;

    /**
     * @ORM\Column(name="action", type="string", length=75)
     */
    protected $action;

    /**
     * @ORM\Column(name="component", type="string", nullable=true)
     */
    protected $component;


    /** get ***********************************************************************************************************/

    public function getPosition()
    {
        return $this->position;
    }

    public function getPresenter()
    {
        return $this->presenter;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getComponent()
    {
        return $this->component;
    }


    /** set ***********************************************************************************************************/

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function setPresenter($presenter)
    {
        $this->presenter = $presenter;

        return $this;
    }

    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    public function setComponent($component)
    {
        $this->component = $component;

        return $this;
    }

}
