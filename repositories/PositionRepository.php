<?php

namespace Wame\ComponentModule\Repositories;

use h4kuna\Gettext\GettextSetup;
use Kdyby\Doctrine\EntityManager;
use Nette\DI\Container;
use Nette\Security\User;
use Wame\Core\Exception\RepositoryException;
use Wame\Core\Repositories\TranslatableRepository;
use Wame\ComponentModule\Entities\PositionEntity;

class PositionRepository extends TranslatableRepository
{
	const STATUS_REMOVE = 0;
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 2;
	
	
	public function __construct(Container $container, EntityManager $entityManager, GettextSetup $translator, User $user, $entityName = null) {
		parent::__construct($container, $entityManager, $translator, $user, PositionEntity::class);
	}
	
	
	/**
	 * Return component status list
	 * 
	 * @return array
	 */
	public function getStatusList()
	{
		return [
			self::STATUS_REMOVE => _('Remove'),
			self::STATUS_ENABLED => _('Enabled'),
			self::STATUS_DISABLED => _('Disabled')
		];
	}
	
	
	/**
	 * Return component status
	 * 
	 * @param int $status
	 * @return string
	 */
	public function getStatus($status)
	{
		return $this->getStatusList()[$status];
	}
	
	
	/**
	 * Create position
	 * 
	 * @param PositionEntity $positionEntity
	 * @return PositionEntity
	 * @throws RepositoryException
	 */
	public function create($positionEntity)
	{
		$this->checkPositionExists(['name' => $positionEntity->name]);

		$this->entityManager->persist($positionEntity);
		
		$this->entityManager->persist($positionEntity->langs);
		
		return $positionEntity;
	}
	
	
	/**
	 * Update position
	 * 
	 * @param PositionEntity $positionEntity
	 */
	public function update($positionEntity)
	{
		$this->checkPositionExists(['name' => $positionEntity->name], $positionEntity->id);
		
		return $positionEntity;
	}
	
	
	/**
	 * Delete positon by criteria
	 * 
	 * @param array $criteria
	 * @param int $status
	 */
	public function delete($criteria = [], $status = self::STATUS_REMOVE)
	{
		$entity = $this->get($criteria);
		$entity->status = $status;
	}
	
	
	/**
	 * Change position status
	 * enabled/disabled
	 * 
	 * @param array $criteria
	 * @param int $status
	 */
	public function changeStatus($criteria = [], $status = null)
	{
		$criteria['status !='] = self::STATUS_REMOVE;
		
		$positions = $this->find($criteria);
		
		foreach ($positions as $position) {
			if ($status) {
				$position->status = $status;
			} else {
				if ($position->status == self::STATUS_DISABLED) {
					$position->status = self::STATUS_ENABLED;
				} else {
					$position->status = self::STATUS_DISABLED;
				}
			}
		}
	}
	
	
	/**
	 * Check position exists by criteria
	 * 
	 * @param array $criteria
	 * @throws RepositoryException
	 */
	public function checkPositionExists($criteria = [], $without = null)
	{
		if ($without) {
			if (!is_array($without)) {
				$without = [$without];
			}
			
			$criteria['id NOT IN'] = $without;
		}
		
		$check = $this->countBy($criteria);
		
		if ($check > 0) {
			throw new RepositoryException(_('Position with this name already exists.'));
		}
	}
	
	
}