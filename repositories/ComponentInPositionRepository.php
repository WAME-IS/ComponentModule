<?php

namespace Wame\ComponentModule\Repositories;

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
		$create = $this->entityManager->persist($componentInPositionEntity);
		
		if (!$create) {
			throw new \Wame\Core\Exception\RepositoryException(_('Component failed to include positions.'));
		}
		
		return $componentInPositionEntity;
	}
	
	
	/**
	 * Delete component in position by criteria
	 * 
	 * @param array $criteria
	 */
	public function delete($criteria)
	{
		$componentInPositionEntity = $this->find($criteria);
		
		$this->remove($componentInPositionEntity);
	}
	
}