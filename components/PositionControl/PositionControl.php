<?php

namespace Wame\ComponentModule\Components;

use Exception;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Paremeters\ArrayParameterSource;
use Wame\ComponentModule\Registers\ComponentRegister;
use Wame\ComponentModule\Renderer\DefaultPositionRenderer;
use Wame\ComponentModule\Renderer\IPositionRenderer;
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

    const POSITION_ID_CLASS = 'pos-%s',
        COMPONENT_ID_CLASS = 'cnt-%s';

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

        if($this->position->status != PositionRepository::STATUS_ENABLED) {
            return;
        }
        
        $this->componentParameters->add(
            new ArrayParameterSource($this->position->getParameters()), 'position', ['priority' => 20]);
        $this->componentParameters->add(
            new ArrayParameterSource(['container' => ['class' => sprintf(self::POSITION_ID_CLASS, $this->positionName)]]), 'positionDefaultClass', ['priority' => 1]);

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
                    $component->setComponentInPosition($componentInPosition);

                    $component->componentParameters->add(
                        new ArrayParameterSource(['container' => ['class' => sprintf(self::COMPONENT_ID_CLASS, $type)]]), 'componentDefaultClass', ['priority' => 1]);
                }

                $this->addComponent($component, $componentName);
            } else {
                throw new InvalidArgumentException("Invalid component type $type");
            }
        }

        return $this;
    }

    protected function attached($control)
    {
        parent::attached($control);
        $this->checkForCycle();
    }

    private function checkForCycle()
    {
        $parent = $this->getParent();
        while ($parent) {
            if ($parent instanceof PositionControl) {
                if ($parent->getPositionName() == $this->getPositionName()) {
                    throw new InvalidArgumentException("Position {$this->getPositionName()} has position {$this->getPositionName()} inside it!");
                }
            }
            $parent = $parent->getParent();
        }
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
        if($this->position->status != PositionRepository::STATUS_ENABLED) {
            return;
        }
        
        $renderer = $this->getRenderer();
        $renderer->render($this);
    }
   
    protected function componentRender()
    {
        //disable default rendering
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
