<?php

namespace Wame\ComponentModule\Components;

use Nette\Application\UI\Control;
use Nette\Object;
use Nette\Utils\Strings;
use Wame\ComponentModule\Entities\PositionUsageEntity;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\ComponentModule\Repositories\PositionUsageRepository;
use Wame\Utils\Exception;

class PositionControlLoader extends Object
{

    /** @var IPositionControlFactory */
    private $IPositionControlFactory;

    /** @var PositionRepository */
    private $positionRepository;

    /** @var PositionUsageRepository */
    private $positionUsageRepository;
    private static $instance;

    public function __construct(IPositionControlFactory $IPositionControlFactory, PositionRepository $positionRepository, PositionUsageRepository $positionUsageRepository)
    {
        $this->IPositionControlFactory = $IPositionControlFactory;
        $this->positionRepository = $positionRepository;
        $this->positionUsageRepository = $positionUsageRepository;

        self::$instance = $this;
    }

    public function load(Control $control)
    {
        $presenterName = $control->getPresenter()->getName();
        $controlName = $control->getUniqueId();

        $positionUsages = $this->positionUsageRepository->find(['presenter' => $presenterName, 'component' => $controlName]);
        foreach ($positionUsages as $positionUsage) {
            $position = $positionUsage->position;
            $positionName = 'position' . Strings::firstUpper($position->name);
            $control->addComponent($this->IPositionControlFactory->create($position), $positionName);
        }
    }

    /**
     * Checks if position usage is saved in database. If not it will be created.
     * 
     * @param Control $control
     * @param string $positionName
     */
    public function check(Control $control, $positionName)
    {
        if ($control->getComponent('position' . Strings::firstUpper($positionName), false)) {
            return;
        }

        $position = $this->positionRepository->get(['name' => $positionName]);
        if (!$position) {
            throw new Exception("Position $positionName does not exist in database.");
        }

        $presenterName = $control->getPresenter()->getName();
        $controlName = $control->getUniqueId();

        $positionUsageEntity = new PositionUsageEntity();
        $positionUsageEntity->setPosition($position);
        $positionUsageEntity->setPresenter($presenterName);
        $positionUsageEntity->setComponent($controlName);

        $this->positionUsageRepository->create($positionUsageEntity);
        
        throw new \Exception("Position $positionName saved to usage table, please reload.");
    }

    public static function checkStatic($component, $positionName)
    {
        self::$instance->check($component, $positionName);
    }
}
