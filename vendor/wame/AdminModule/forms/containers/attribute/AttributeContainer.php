<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Containers;

use Nette\Forms\Container;
use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;

interface IAttributeContainerFactory extends IBaseContainer
{
	/** @return AttributeContainer */
	public function create();
}

class AttributeContainer extends BaseContainer
{
    /** {@inheritDoc} */
    public function configure() 
	{
        $this->addDynamic('attribute', function (Container $container) {
            $container->addText('name', _('Name'));
            $container->addText('value', _('Value'));
        });
    }

    
    /** {@inheritDoc} */
	public function setDefaultValues($entity, $langEntity = null)
	{
//        $this['position']->setDefaultValue($entity->getParameter('position'));
	}
    
    public function update($form, $values)
    {
        parent::update($form, $values);
        
        foreach($this->getHttpData()['attribute'] as $attr) {
            $form->getEntity()->setParameter('container', $form->getEntity()->getParameter('container') + [$attr['name'] => $attr['value']]);
        }
    }

}