<?php

namespace Wame\ComponentModule\Repositories;

use Wame\ComponentModule\Entities\PositionUsageEntity;
use Wame\Core\Repositories\BaseRepository;


class PositionUsageRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(PositionUsageEntity::class);
    }

    
    /**
     * Add position usage
     * 
     * @param PositionUsageEntity $positionUsageEntity
     *
     * @return PositionUsageEntity
     */
    public function create(PositionUsageEntity $positionUsageEntity)
    {
        $this->entityManager->persist($positionUsageEntity);
        $this->entityManager->flush($positionUsageEntity);
        
        return $positionUsageEntity;
    }
    
}
