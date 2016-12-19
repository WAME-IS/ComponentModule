<?php

namespace Wame\ComponentModule\Traits;

use Wame\Core\Registers\StatusTypeRegister;

trait TComponentStatusType
{
    /** @var StatusTypeRegister */
    protected $statusTypeRegister;
    
    /** @var string */
    protected $statusType;
    
    
    public function injectStatusTypeRegister(StatusTypeRegister $statusTypeRegister)
    {
        $this->statusTypeRegister = $statusTypeRegister;
    }
    
    
    /**
     * Get entity by status type
     * 
     * @param type $strict  strict mode
     * @return BaseEntity
     * @throws \Exception
     */
    public function getEntityByStatusType($strict = false)
    {
        $type = $this->getStatusType();
        
        if($type) {
            $entity = $this->getStatus()->get($type->getEntityName());

            return $entity;
        } else if($strict) {
            throw new \Exception("Could not find entity by statusType [$type]. Did you set correct statusType?");
        }
        
        return null;
    }
    
    
    /**
     * Get status type register
     * 
     * @return type
     */
    protected function getStatusTypeRegister()
    {
        return $this->statusTypeRegister;
    }
    
    protected function getStatusTypeName()
    {
        return $this->statusType ?: $this->getComponentParameter('statusType');
    }
    
    /**
     * Get status type
     * 
     * @return type
     */
    protected function getStatusType()
    {
        return $this->getStatusTypeRegister()->getByName($this->getStatusTypeName());
    }
    
    /**
     * Disable render by status entity
     * 
     * @return bool
     */
    private function disableRenderByStatusEntity()
    {
        $filterByStatusEntity = $this->getComponentParameter('filterByStatusEntity');
        
        $willRenderer = true;
        
        if($filterByStatusEntity !== null) {
            $entity = $this->getEntityByStatusType();

            switch($filterByStatusEntity)
            {
                case 1: // entity found
                    $willRenderer = ($entity !== null); break;
                case 2: // entity not found
                    $willRenderer = ($entity === null); break;
                case 3: // never
                    $willRenderer = false; break;
            }
        }
        
        return !$willRenderer;
    }
    
    /**
     * Set status type
     * 
     * @param string $type
     * @return this
     */
    public function setStatusType($type)
    {
        $this->statusType = $type;
        
        return $this;
    }
    
}
