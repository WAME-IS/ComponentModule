<?php

namespace Wame\ComponentModule\Doctrine\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Repositories\PositionRepository;
use Zenify\DoctrineFilters\Contract\ConditionalFilterInterface;


final class PositionStatusFilter implements ConditionalFilterInterface
{
    /** @var boolean */
    private $enabled = true;

    
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $entity, string $alias) : string
    {
        if ($entity->getName() == PositionEntity::class && $this->enabled) {
            return sprintf('%s.status = %s', $alias, PositionRepository::STATUS_ENABLED);
        }
        
        return '';
    }

    
    public function setEnabled($status)
    {
        $this->enabled = $status;
        
        return $this;
    }
    
    
    public function isEnabled() : bool
    {
        return $this->enabled;
    }

}
