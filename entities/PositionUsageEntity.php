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
     * @ORM\Column(name="presenter", type="string")
     */
    protected $presenter;

    /**
     * @ORM\Column(name="component", type="string", nullable=true)
     */
    protected $component;

    /**
     * @ORM\ManyToOne(targetEntity="PositionEntity", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", nullable=false)
     */
    protected $position;

    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    function getPresenter()
    {
        return $this->presenter;
    }

    function setPresenter($presenter)
    {
        $this->presenter = $presenter;
    }

    function getComponent()
    {
        return $this->component;
    }

    function setComponent($component)
    {
        $this->component = $component;
    }
}
