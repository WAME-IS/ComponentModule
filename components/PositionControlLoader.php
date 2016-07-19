<?php

namespace Wame\ComponentModule\Components;

class PositionControlLoader extends \Nette\Object
{
    
    /** @var IPositionControlFactory */
    private $IPositionControlFactory;
    
    public function __construct(IPositionControlFactory $IPositionControlFactory)
    {
        $this->IPositionControlFactory = $IPositionControlFactory;
    }
    
    public function load(\Nette\Application\UI\Control $control)
    {
        $control->addComponent($this->IPositionControlFactory->create('header'), 'positionHeader');
    }
    
}
