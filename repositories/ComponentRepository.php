<?php

namespace Wame\ComponentModule\Repositories;

use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Entities\ComponentLangEntity;

class ComponentRepository extends \Wame\Core\Repositories\BaseRepository
{
	const STATUS_REMOVE = 0;
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 2;
	
	
	public function __construct(\Nette\DI\Container $container, \Kdyby\Doctrine\EntityManager $entityManager, \h4kuna\Gettext\GettextSetup $translator, \Nette\Security\User $user, $entityName = null) {
		parent::__construct($container, $entityManager, $translator, $user, ComponentEntity::class);
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
	 * Create component
	 * 
	 * @param ComponentEntity $componentEntity
	 * @return ComponentEntity
	 * @throws \Wame\Core\Exception\RepositoryException
	 */
	public function create($componentEntity)
	{
		$create = $this->entityManager->persist($componentEntity);
		
		$this->entityManager->persist($componentEntity->langs);
		
		if (!$create) {
			throw new \Wame\Core\Exception\RepositoryException(_('Component could not be created.'));
		}
		
		return $componentEntity;
	}
	
	
	/**
	 * Delete component by criteria
	 * 
	 * @param array $criteria
	 * @param int $status
	 */
	public function delete($criteria = [], $status = self::STATUS_REMOVE)
	{
		$entity = $this->get($criteria);
		$entity->status = $status;
	}
	
}