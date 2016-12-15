<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;

interface ITemplateContainerFactory extends IBaseContainer
{
	/** @return TemplateContainer */
	public function create();
}

class TemplateContainer extends BaseContainer
{
    /** {@inheritDoc} */
    public function configure() 
	{
		$this->addText('template', _('Template'))
				->setAttribute('placeholder', 'default.latte');
    }

    /** {@inheritDoc} */
	public function setDefaultValues($entity, $langEntity = null)
	{
        $this['template']->setDefaultValue($entity->getParameter('template'));
	}

    /** {@inheritDoc} */
    public function create($form, $values)
    {
        $form->getEntity()->setParameter('template', $values['template']);
    }

    /** {@inheritDoc} */
    public function update($form, $values)
    {
        $form->getEntity()->setParameter('template', $values['template']);
    }

}