<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Groups;

use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\DynamicObject\Forms\Groups\BaseGroup;


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
