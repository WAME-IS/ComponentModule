<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Groups;

use Wame\DynamicObject\Forms\Groups\BaseGroup;
use Wame\DynamicObject\Registers\Types\IBaseContainer;

interface IContainerGroupFactory extends IBaseContainer
{
	/** @return ContainerGroup */
	function create();
}

class ContainerGroup extends BaseGroup
{
    /** {@inheritDoc} */
    public function getText()
    {
        return _('Container');
    }

}