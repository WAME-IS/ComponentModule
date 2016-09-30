<?php

namespace Wame\ComponentModule\Doctrine\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Zenify\DoctrineFilters\Contract\ConditionalFilterInterface;
use Wame\ComponentModule\Repositories\ComponentRepository;


final class ComponentStatusFilter implements ConditionalFilterInterface
{
    /** @var boolean */
    private $enabled = true;

    
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $entity, $alias)
    {
        if ($entity->getName() == 'Wame\ComponentModule\Entities\ComponentEntity' && $this->enabled) {
            return sprintf('%s.status = %s', $alias, ComponentRepository::STATUS_ENABLED);
        }
        
        return '';
    }

    
    public function setEnabled($status)
    {
        $this->enabled = $status;
        
        return $this;
    }
    
    
    public function isEnabled() 
    {
        return $this->enabled;
    }

}
