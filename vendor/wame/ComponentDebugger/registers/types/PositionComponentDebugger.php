<?php

namespace Wame\ComponentModule\Vendor\Wame\ComponentDebugger\Registers\Types;

use Wame\ComponentDebugger\Registers\Types\IComponentDebuggerType;

class PositionComponentDebugger implements IComponentDebuggerType
{

    public function getControlType()
    {
        return \Wame\ComponentModule\Components\PositionControl::class;
    }

    public function getTitle()
    {
        return "Position";
    }

    public function getBorderColor()
    {
        return 'blue';
    }
    
    public function getControlData($control)
    {
        return $control->getPositionName();
    }
}
