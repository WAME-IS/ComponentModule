<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Events;

use Nette\Object;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Repositories\PositionRepository;

class ComponentFormListener extends Object 
{
	/** @var ComponentInPositionRepository */
	private $componentInPositionRepository;
	
	/** @var PositionRepository */
	private $positionRepository;
	
	
	public function __construct(
		ComponentRepository $componentRepository,
		ComponentInPositionRepository $componentInPositionRepository,
		PositionRepository $positionRepository
	) {
		$this->componentInPositionRepository = $componentInPositionRepository;
		$this->positionRepository = $positionRepository;
		
		$componentRepository->onCreate[] = [$this, 'onCreate'];
		$componentRepository->onUpdate[] = [$this, 'onUpdate'];
		$componentRepository->onDelete[] = [$this, 'onDelete'];
	}

	
	public function onCreate($form, $values, $componentEntity) 
	{
		if (count($values['position']) > 0) {
			$positions = $this->getPositions($values['position']);
			
			foreach ($values['position'] as $position) {
				$componentInPositionEntity = new ComponentInPositionEntity();
				$componentInPositionEntity->component = $componentEntity;
				$componentInPositionEntity->position = $positions[$position];
				$componentInPositionEntity->setSort(0);
				$componentInPositionEntity->setParameters(null);
				
				$this->componentInPositionRepository->create($componentInPositionEntity);
			}
		}
	}
	
	
	public function onUpdate($form, $values, $componentEntity)
	{
		if (count($values['position']) > 0) {
			$positions = $this->getPositions($values['position']);
			
			$componentInPosition = $this->getComponentPositions($componentEntity);
			
			$removeList = $componentInPosition;

			foreach ($positions as $positionId => $position) {
				if (!isset($componentInPosition[$positionId])) {
					$componentInPositionEntity = new ComponentInPositionEntity();
					$componentInPositionEntity->component = $componentEntity;
					$componentInPositionEntity->position = $position;
					$componentInPositionEntity->setSort($this->componentInPositionRepository->getNextSort(['position' => $position]));
					$componentInPositionEntity->setParameters(null);

					$this->componentInPositionRepository->create($componentInPositionEntity);
				}
				
				unset($removeList[$positionId]);
			}
			
			$this->componentInPositionRepository->remove(['component' => $componentEntity, 'position.id IN' => $removeList]);
		} else {
			$this->componentInPositionRepository->remove(['component' => $componentEntity]);
		}
	}
	
	
	public function onDelete()
	{
		
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

}
