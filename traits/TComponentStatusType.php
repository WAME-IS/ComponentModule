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
    
    
    public function getEntityByStatusType()
    {
        $type = $this->getStatusType();
        
        if($type) {
            $entity = $this->getStatus()->get($type->getEntityName());

            return $entity;
        }
    }
    
    
    protected function getStatusTypeRegister()
    {
        return $this->statusTypeRegister;
    }
    
    protected function getStatusTypeName()
    {
        return $this->statusType ?: $this->getComponentParameter('statusType');
    }
    
    protected function getStatusType()
    {
        return $this->getStatusTypeRegister()->getByName($this->getStatusTypeName());
    }
    
    private function disableRenderByStatusEntity()
    {
        $filterByStatusEntity = $this->getComponentParameter('filterByStatusEntity');
        
        $willRenderer = true;
        
        if($filterByStatusEntity != null) {
            $entity = $this->getEntityByStatusType();

            switch($filterByStatusEntity)
            {
                case 0: // entity found
                    $willRenderer = ($entity != null); break;
                case 1: // entity not found
                    $willRenderer = ($entity == null); break;
                case 2: // never
                    $willRenderer = false; break;
            }
        }
        
        return !$willRenderer;
    }
    
    public function setStatusType($type)
    {
        $this->statusType = $type;
        
        return $this;
    }
    
}
