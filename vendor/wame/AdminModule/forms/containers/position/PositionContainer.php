<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;

interface IPositionContainerFactory extends IBaseContainer
{
	/** @return PositionContainer */
	public function create();
}

/**
 * @method array getPositionList()
 * @method setPositionList(array)
 */
class PositionContainer extends BaseContainer
{
    /** @var PositionRepository */
    private $positionRepository;
    
    /** @var ComponentInPositionRepository */
    private $componentInPositionRepository;
    
    
    public function __construct(
        \Nette\DI\Container $container, 
        PositionRepository $positionRepository,
        ComponentInPositionRepository $componentInPositionRepository
    ) {
        parent::__construct($container);
        
        $this->positionRepository = $positionRepository;
        $this->componentInPositionRepository = $componentInPositionRepository;
    }
    
    
    /** {@inheritDoc} */
    public function configure() 
	{
        $positionList = $this->positionRepository->findPairs(['status' => PositionRepository::STATUS_ENABLED], 'name', ['name']);
        
		$this->addMultiSelect('position', _('Position'), $positionList)
				->setAttribute('placeholder', 'default.latte');
    }

    
    /** {@inheritDoc} */
	public function setDefaultValues($entity, $langEntity = null)
	{
        $this['position']->setDefaultValue($entity->getParameter('position'));
	}

//    /** {@inheritDoc} */
//    public function create($form, $values)
//    {
//        $entity = method_exists($form, 'getLangEntity') && property_exists($form->getLangEntity(), 'title') ? $form->getLangEntity() : $form->getEntity();
//        $entity->setTitle($values['title']);
//    }

    /** {@inheritDoc} */
    public function postUpdate($form, $values)
    {
        $entity = $form->getEntity();
        
        if(count($values['position'])) {
            $positions = $this->getPositions($values['position']);
            $componentInPosition = $this->getComponentPositions($entity);
            $removeList = $componentInPosition;

            foreach ($positions as $positionId => $position) {
                if (!isset($componentInPosition[$positionId])) {
                    $this->attach($form->getEntity(), $position);
                }

                unset($removeList[$positionId]);
            }

            $this->componentInPositionRepository->remove(['component' => $entity, 'position.id IN' => $removeList]);
        } else {
			$this->componentInPositionRepository->remove(['component' => $entity]);
		}
    }
    
    /**
	 * Get positions
	 * 
	 * @param array $positionIds
	 * @return array
	 */
	private function getPositions($positionIds)
	{
		$return = [];
		
		$positions = $this->positionRepository->find(['id IN' => $positionIds]);
		
		foreach ($positions as $position) {
			$return[$position->id] = $position;
		}
		
		return $return;
	}
    
    /**
	 * Get component positions
	 * 
	 * @param ComponentEntity $componentEntity
	 * @return array
	 */
	private function getComponentPositions($componentEntity)
	{
		$return = [];
		
		$positions = $this->componentInPositionRepository->find(['component' => $componentEntity]);

		foreach ($positions as $position) {
			$return[$position->position->id] = $position->position->id;
		}
		
		return $return;
	}
    
    private function attach($component, $position, $sort = null, $parameters = null)
    {
        $componentInPositionEntity = new ComponentInPositionEntity();
        $componentInPositionEntity->component = $component;
        $componentInPositionEntity->position = $position;
        $componentInPositionEntity->setSort($sort ?: $this->componentInPositionRepository->getNextSort(['position' => $position]));
        $componentInPositionEntity->setParameters($parameters);
        
        $this->componentInPositionRepository->create($componentInPositionEntity);
    }

}