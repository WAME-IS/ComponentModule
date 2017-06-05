<?php

namespace Wame\ComponentModule\Doctrine\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Zenify\DoctrineFilters\Contract\ConditionalFilterInterface;

final class ComponentStatusFilter implements ConditionalFilterInterface
{
    /** @var boolean */
    private $enabled = true;

    
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $entity, $alias) : string
    {
        if ($entity->getName() == ComponentEntity::class && $this->enabled) {
            return sprintf('%s.status = %s', $alias, ComponentRepository::STATUS_ENABLED);
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
