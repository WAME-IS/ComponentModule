<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;

interface ITypeContainerFactory extends IBaseContainer
{
	/** @return TypeContainer */
	public function create();
}

class TypeContainer extends BaseContainer
{
    /** {@inheritDoc} */
    public function configure() 
	{
		$this->addHidden('type');
    }

    /** {@inheritDoc} */
    public function create($form, $values)
    {
        $form->getEntity()->setType($this->getPresenter()->getComponentIdentifier());
    }

    /** {@inheritDoc} */
    public function update($form, $values)
    {
//        $form->getEntity()->setParameter('layout', $values['layout']);
        $form->getEntity()->setType($this->getPresenter()->getComponentIdentifier());
    }

}