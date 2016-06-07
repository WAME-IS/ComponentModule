<?php

namespace Wame\ComponentModule\Repositories;

use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;

class ComponentInPositionRepository extends \Wame\Core\Repositories\BaseRepository
{
	const STATUS_REMOVE = 0;
	const STATUS_ACTIVE = 1;
	
	public function __construct(\Nette\DI\Container $container, \Kdyby\Doctrine\EntityManager $entityManager, \h4kuna\Gettext\GettextSetup $translator, \Nette\Security\User $user, $entityName = null) {
		parent::__construct($container, $entityManager, $translator, $user, ComponentInPositionEntity::class);
	}
	
	/**
	 * Add component in position
	 * 
	 * @param ComponentInPositionEntity $componentInPositionEntity
	 * @return ComponentInPositionEntity
	 * @throws \Wame\Core\Exception\RepositoryException
	 */
	public function create($componentInPositionEntity)
	{
		$find = $this->countBy(['component.id' => $componentInPositionEntity->component->id, 'position.id' => $componentInPositionEntity->position->id]);

		if ($find > 0) {
			throw new \Wame\Core\Exception\RepositoryException(_('This component is already in this position.'));
		} else {
			$create = $this->entityManager->persist($componentInPositionEntity);

			if (!$create) {
				throw new \Wame\Core\Exception\RepositoryException(_('Component failed to include positions.'));
			}
		}
		
		return $componentInPositionEntity;
	}
	
	
	/**
	 * Update component in position
	 * 
	 * @param ComponentInPositionEntity $componentInPositionEntity
	 * @return ComponentInPositionEntity
	 */
	public function update($componentInPositionEntity)
	{
		return $componentInPositionEntity;
	}
	
	
	/**
	 * Get component positions 
	 * 
	 * @param ComponentEntity $component
	 * @return array
	 */
	public function getPositions($component)
	{
		$return = [];
		
		$positions = $this->find(['component' => $component]);
		
		foreach ($positions as $position) {
			$return[$position->position->id] = $position->position;
		}
		
		return $return;
	}
	
}