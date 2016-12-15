<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Groups;

use Wame\DynamicObject\Forms\Groups\BaseGroup;
use Wame\DynamicObject\Registers\Types\IBaseContainer;

interface IComponentGroupFactory extends IBaseContainer
{
	/** @return ComponentGroup */
	function create();
}

class ComponentGroup extends BaseGroup
{
    /** {@inheritDoc} */
    public function getText()
    {
        return _('Component');
    }

}