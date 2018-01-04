<?php

namespace Wame\ComponentModule\Components;

use DateTime;
use Nette\Application\UI\Control;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Object;
use Nette\Utils\Strings;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Entities\PositionLangEntity;
use Wame\ComponentModule\Entities\PositionUsageEntity;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\ComponentModule\Repositories\PositionUsageRepository;
use Wame\Utils\Date;


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


    public function __construct(
        IPositionControlFactory $IPositionControlFactory,
        PositionRepository $positionRepository,
        PositionUsageRepository $positionUsageRepository
    ) {
        $this->IPositionControlFactory = $IPositionControlFactory;
        $this->positionRepository = $positionRepository;
        $this->positionUsageRepository = $positionUsageRepository;

        self::$instance = $this;
    }


    /**
     * Load and create positions
     *
     * @param Control $control
     */
    public function load(Control $control)
    {
        $presenter = $control->getPresenter();
        $presenterName = $presenter->getName();
        $presenterAction = $presenter->getAction();
        $controlName = $control->getUniqueId();

        $this->loadPresenter($presenterName, $presenterAction);

        if (!isset($this->positionsInPresenter[$controlName])) {
            return; //has no positions
        }

        foreach ($this->positionsInPresenter[$controlName] as $position) {
            $positionName = 'position' . Strings::firstUpper($position->name);

            if (!isset($control->getComponents()[$positionName]) && $position->getStatus() == PositionRepository::STATUS_ENABLED) {
                $control->addComponent($this->IPositionControlFactory->create($position), $positionName);
            }
        }
    }


    /**
     * Load presenter
     * Get position usage in this presenter
     *
     * @param string $presenterName
     * @param string $action
     */
    private function loadPresenter($presenterName, $action)
    {
        if (!$this->positionsInPresenter) {
            try {
                $positionUsages = $this->positionUsageRepository->find(['presenter' => $presenterName, 'action' => $action]);

                foreach ($positionUsages as $usage) {
                    if (!isset($this->positionsInPresenter[$usage->component])) {
                        $this->positionsInPresenter[$usage->getComponent()] = [];
                    }

                    $this->positionsInPresenter[$usage->getComponent()][$usage->getPosition()->getId()] = $usage->getPosition();
                }
            } catch (\Exception $e) {

            }
        }
    }


    /**
     * Checks if position usage is saved in database.
     * If not it will be created.
     *
     * @param Control $control
     * @param string $positionName
     *
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

        $positionUsageEntity = new PositionUsageEntity();
        $positionUsageEntity->setPosition($position);
        $positionUsageEntity->setPresenter($control->getPresenter()->getName());
        $positionUsageEntity->setAction($control->getPresenter()->getAction());
        $positionUsageEntity->setComponent($control->getUniqueId());

        $this->positionUsageRepository->create($positionUsageEntity);

        return false;
    }


    /**
     * Create new position
     *
     * @param $position
     * @return PositionEntity
     *
     * @throws \Wame\Core\Exception\RepositoryException
     */
    private function createPosition($position)
    {
        $newPositionEntity = new PositionEntity();

        $newPositionEntity->setName($position);
        $newPositionEntity->setStatus(PositionRepository::STATUS_ENABLED);
        $newPositionEntity->setCreateDate(Date::toDateTime(Date::NOW));

        $newPositionLangEntity = new PositionLangEntity();
        $newPositionLangEntity->setPosition($newPositionEntity);
        $newPositionLangEntity->setTitle($position);
        $newPositionLangEntity->setEditDate(Date::toDateTime(Date::NOW));
        $newPositionLangEntity->setLang($this->positionRepository->lang);

        $newPositionEntity->addLang($this->positionRepository->lang, $newPositionLangEntity);

        $this->positionRepository->create($newPositionEntity);

        return $newPositionEntity;
    }


    /**
     * Check exists component in position
     *
     * @param Control $control
     * @param string $positionName
     *
     * @return bool
     */
    public static function checkStatic($control, $positionName)
    {
        return self::$instance->check($control, $positionName);
    }

}
