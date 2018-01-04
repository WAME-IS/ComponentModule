<?php

namespace Wame\ComponentModule\Components;

use Doctrine\Common\Collections\Criteria;
use Exception;
use Nette\Application\UI\Control;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Tracy\Debugger;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Paremeters\ArrayParameterSource;
use Wame\ComponentModule\Registers\ComponentRegister;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\ComponentModule\Renderer\PositionRenderer;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\Core\Components\BaseControl;
use Wame\ListControl\Components\ISimpleEmptyListControlFactory;
use Wame\ListControl\Components\ListControl;
use Wame\ListControl\Renderer\IListRenderer;


interface IPositionControlFactory
{
    /** @return PositionControl */
    public function create($position);
}


class PositionControl extends ListControl
{
    const POSITION_ID_CLASS = 'pos-%s';
    

    /** @var PositionRepository */
    private $positionRepository;

    /** @var ComponentRegister */
    private $componentRegister;

    /** @var string */
    private $positionName;

    /** @var PositionEntity */
    private $position;

    /** @var Control[] */
    private $listComponents;

    /** @var ISimpleEmptyListControlFactory */
    private $ISimpleEmptyListControlFactory;


    public function __construct(
        Container $container,
        PositionRepository $positionRepository,
        ComponentRegister $componentRegister,
        ISimpleEmptyListControlFactory $ISimpleEmptyListControlFactory,
        $position
    ) {
        parent::__construct($container);

        $this->positionRepository = $positionRepository;
        $this->componentRegister = $componentRegister;
        $this->ISimpleEmptyListControlFactory = $ISimpleEmptyListControlFactory;

        $this->componentParameters->add(new ArrayParameterSource(['listContainer' => ['tag' => null], 'listItemContainer' => ['tag' => null]]), 'listContainers', ['priority' => 1]);

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
            $positionName = $position->getName();
        } elseif (is_string($position)) {
            $positionEntity = $this->positionRepository->get(['name' => $position, 'status' => PositionRepository::STATUS_ENABLED]);

            if (!$positionEntity) {
                throw new Exception("Position $position does not exist in database.");
            }

            $positionName = $position;
        } else {
            throw new InvalidArgumentException("Argument position has wrong type.");
        }

        $this->positionName = $positionName;
        $this->position = $positionEntity;

        if ($this->position->getStatus() == PositionRepository::STATUS_ENABLED) {
            $this->getListComponents();

            $this->componentParameters->add(new ArrayParameterSource($this->position->getParameters()), 'position', ['priority' => 20]);
            $this->componentParameters->remove('componentDefaultClass');
            $this->componentParameters->add(new ArrayParameterSource(['container' => ['class' => sprintf(self::POSITION_ID_CLASS, $this->positionName)]]), 'positionDefaultClass', ['priority' => 1]);
        }
    }


    /**
     * Register components
     *
     * @return PositionControl
     */
    public function getListComponents()
    {
        if (is_array($this->listComponents)) {
            return $this->listComponents;
        }

        $this->listComponents = [];

        if ($this->position->getStatus() != PositionRepository::STATUS_ENABLED) {
            return $this->listComponents;
        }

        $criteria = Criteria::create()->orderBy(['sort' => Criteria::ASC]);
        $componentsInPosition = $this->position->getComponents()->matching($criteria);

        foreach ($componentsInPosition as $componentInPosition) {
            if ($componentInPosition->getComponent()->getStatus() != ComponentRepository::STATUS_ENABLED) {
                continue;
            }

            $type = $componentInPosition->getComponent()->getType();

            $componentType = $this->componentRegister->getByName($type);

            try {
                if ($componentType) {
                    $componentName = $this->uniqeComponentName($componentInPosition->getComponentInPositionName());
                    $component = $componentType->createComponent($componentInPosition);

                    if ($component instanceof BaseControl) {
                        $component->setComponentInPosition($componentInPosition);

                        //TODO remove
                        $component->componentParameters->add(new ArrayParameterSource(['container' => ['class' => sprintf(BaseControl::COMPONENT_ID_CLASS, $type)]]), 'componentInPositionClass', ['priority' => 0]);
                    }

                    $this->listComponents[$componentName] = $component;
                    $this->addComponent($component, $componentName);
                } else {
                    throw new InvalidArgumentException("Invalid component type $type");
                }
            } catch (Exception $e) {
                Debugger::log($e);
                Debugger::barDump($e->getMessage() . ', Error has been logged on ./log folder.', $type);
            }
        }

        return $this->listComponents;
    }


    public function getListComponent($id)
    {
        $components = $this->getListComponents();

        if (isset($components[$id])) {
            return $components[$id];
        }
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
                    throw new InvalidArgumentException("Position {$this->getPositionName()} has position {$parent->getPositionName()} inside it!");
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


    /**
     * Gets renderer used to render components in list
     *
     * @return IListRenderer
     */
    public function getRenderer()
    {
        if (!$this->renderer) {
            $this->renderer = new PositionRenderer();
        }

        return $this->renderer;
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
     * Create no items control
     *
     * @return \Nette\ComponentModel\IComponent|\Wame\ListControl\Components\SimpleEmptyListControl
     */
    public function createComponentNoItems()
    {
        return $this->ISimpleEmptyListControlFactory->create();
    }

}
