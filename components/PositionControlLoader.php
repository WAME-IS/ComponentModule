<?php

namespace Wame\ComponentModule\Components;

use DateTime;
use Nette\Application\UI\Control;
use Nette\Object;
use Nette\Utils\Strings;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Entities\PositionLangEntity;
use Wame\ComponentModule\Entities\PositionUsageEntity;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\ComponentModule\Repositories\PositionUsageRepository;

class PositionControlLoader extends Object
{

    /** @var IPositionControlFactory */
    private $IPositionControlFactory;

    /** @var PositionRepository */
    private $positionRepository;

    /** @var PositionUsageRepository */
    private $positionUsageRepository;
    
    /** @var array */
    private $positionsInPresenter;
    
    /** @var static */
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

        $this->loadPresenter($presenterName);
        
        if(!isset($this->positionsInPresenter[$controlName])) {
            return; //has no positions
        }
        
        foreach ($this->positionsInPresenter[$controlName] as $position) {
            $positionName = 'position' . Strings::firstUpper($position->name);
            
            if (!isset($control->getComponents()[$positionName])) {
                $control->addComponent($this->IPositionControlFactory->create($position), $positionName);
            }
        }
    }
    
    private function loadPresenter($presenterName) {
        if(!$this->positionsInPresenter) {
            $positionUsages = $this->positionUsageRepository->find(['presenter' => $presenterName]);
            foreach($positionUsages as $usage) {
                if(!isset($this->positionsInPresenter[$usage->component])) {
                    $this->positionsInPresenter[$usage->component] = [];
                }
                $this->positionsInPresenter[$usage->component][] = $usage->position;
            }
        }
    }

    /**
     * Checks if position usage is saved in database. If not it will be created.
     * 
     * @param Control $control
     * @param string $positionName
     * @return boolean Whenever position is loaded
     */
    public function check(Control $control, $positionName)
    {
        if ($control->getComponent('position' . Strings::firstUpper($positionName), false)) {
            return true;
        }

        $position = $this->positionRepository->get(['name' => $positionName]);
        if (!$position) {
            $position = $this->createPosition($positionName);
        }

        $presenterName = $control->getPresenter()->getName();
        $controlName = $control->getUniqueId();

        $positionUsageEntity = new PositionUsageEntity();
        $positionUsageEntity->setPosition($position);
        $positionUsageEntity->setPresenter($presenterName);
        $positionUsageEntity->setComponent($controlName);

        $this->positionUsageRepository->create($positionUsageEntity);
        
        return false;
    }

    private function createPosition($position)
    {
        $newPositionEntity = new PositionEntity();

        $newPositionEntity->setName($position);
        $newPositionEntity->setStatus(PositionRepository::STATUS_ENABLED);
        $newPositionEntity->setCreateDate(new DateTime());

        $newPositionLangEntity = new PositionLangEntity();

        $newPositionLangEntity->setPosition($newPositionEntity);
        $newPositionLangEntity->setTitle($position);
        $newPositionLangEntity->setEditDate(new DateTime());
        $newPositionLangEntity->setLang($this->positionRepository->lang);
        $newPositionEntity->addLang($this->positionRepository->lang, $newPositionLangEntity);

        $this->positionRepository->create($newPositionEntity);

        return $newPositionEntity;
    }

    public static function checkStatic($component, $positionName)
    {
        return self::$instance->check($component, $positionName);
    }
}
