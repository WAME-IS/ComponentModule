<?php

namespace Wame\ComponentModule\Repositories;

use h4kuna\Gettext\GettextSetup;
use Kdyby\Doctrine\EntityManager;
use Nette\DI\Container;
use Nette\Security\User;
use Wame\ComponentModule\Entities\PositionUsageEntity;
use Wame\Core\Repositories\BaseRepository;

class PositionUsageRepository extends BaseRepository
{

    public function __construct(Container $container, EntityManager $entityManager, GettextSetup $translator, User $user, $entityName = null)
    {
        parent::__construct($container, $entityManager, $translator, $user, PositionUsageEntity::class);
    }

    /**
     * Add position usage
     * 
     * @param PositionUsageEntity $positionUsageEntity
     * @return PositionUsageEntity
     */
    public function create(PositionUsageEntity $positionUsageEntity)
    {
        $this->entityManager->persist($positionUsageEntity);
        $this->entityManager->flush($positionUsageEntity);
        return $positionUsageEntity;
    }
}
