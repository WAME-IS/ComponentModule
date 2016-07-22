<?php

namespace Wame\ComponentModule\Components;

use ComponentModule\Renderer\DefaultPositionRenderer;
use ComponentModule\Renderer\IPositionRenderer;
use Exception;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Paremeters\ArrayParameterSource;
use Wame\ComponentModule\Registers\ComponentRegister;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\Core\Components\BaseControl;

interface IPositionControlFactory
{

    /** @return PositionControl */
    public function create($position);
}

class PositionControl extends BaseControl
{

    const COMPONENT_TYPE_CLASS = 'pos-%s';

    /** @var PositionRepository */
    private $positionRepository;

    /** @var ComponentRegister */
    private $componentRegister;

    /** @var string */
    private $positionName;

    /** @var PositionEntity */
    private $position;

    /** @var ComponentInPositionEntity[] */
    private $componentsInPosition;

    /** @var IPositionRenderer */
    private $renderer;

    public function __construct(
    Container $container, PositionRepository $positionRepository, ComponentRegister $componentRegister, $position
    )
    {
        parent::__construct($container);

        $this->positionRepository = $positionRepository;
        $this->componentRegister = $componentRegister;

        $this->setPosition($position);
    }

    /**
     * Set position
     * 
     * @param string $position
     * @return PositionControl
     */
    public function setPosition($position)
    {
        if (is_object($position) && $position instanceof PositionEntity) {
            $positionEntity = $position;
            $positionName = $position->name;
        } elseif (is_string($position)) {
            $positionEntity = $this->positionRepository->get(['name' => $position, 'status' => PositionRepository::STATUS_ENABLED]);
            if (!$positionEntity) {
                throw new Exception("Position $position does not exist in database.");
            }
            $positionName = $position;
        } else {
            throw new InvalidArgumentException("Argument position has wrong type.");
        }

        $this->position = $positionEntity;
        $this->positionName = $positionName;

        $this->componentParameters->add(
            new ArrayParameterSource($this->position->getParameters()), 'position', 20);
        $this->componentParameters->add(
            new ArrayParameterSource(['container' => ['class' => sprintf(self::COMPONENT_TYPE_CLASS, $this->positionName)]]), 'positionDefaultClass', 1);

        $this->loadComponents();

        return $this;
    }

    /**
     * Register components
     * 
     * @return PositionControl
     */
    private function loadComponents()
    {
        $this->componentsInPosition = $this->position->getComponents();

        foreach ($this->componentsInPosition as $componentInPosition) {

            if ($componentInPosition->component->status != ComponentRepository::STATUS_ENABLED) {
                continue;
            }

            $type = $componentInPosition->component->type;

            $componentType = $this->componentRegister->getByName($type);
            if ($componentType) {

                $componentName = $this->uniqeComponentName($componentInPosition->getComponentInPositionName());
                $component = $componentType->createComponent($componentInPosition);

                if ($component instanceof BaseControl) {
                    $component->setComponentInPosition($type, $componentInPosition);
                }

                $this->addComponent($component, $componentName);
            } else {
                throw new InvalidArgumentException("Invalid component type $type");
            }
        }

        return $this;
    }

    private function uniqeComponentName($originalName)
    {
        $name = $originalName;
        $i = 1;
        while ($this->getComponent($name, false)) {
            $name = $originalName . $i;
            $i++;
        }
        return $name;
    }

    public function render()
    {
        $renderer = $this->getRenderer();
        $renderer->render($this);
    }

    /**
     * Gets renderer used to render components in position
     * 
     * @return IPositionRenderer
     */
    function getRenderer()
    {
        if (!$this->renderer) {
            $this->renderer = new DefaultPositionRenderer();
        }
        return $this->renderer;
    }

    /**
     * Sets renderer used to render components in position
     * 
     * @param IPositionRenderer $renderer
     */
    function setRenderer(IPositionRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return string
     */
    function getPositionName()
    {
        return $this->positionName;
    }

    /**
     * @return PositionEntity
     */
    function getPosition()
    {
        return $this->position;
    }

    /**
     * @return ComponentInPositionEntity[]
     */
    function getComponentsInPosition()
    {
        return $this->componentsInPosition;
    }
}
