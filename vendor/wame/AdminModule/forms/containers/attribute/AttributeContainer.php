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
//        $attributesContainer = $this->addContainer('attribute');
        
//        $button = $this->addSubmit('add', _('Add'));
        
        $this->addDynamic('attribute', function (Container $attributeContainer) {
            $attributeContainer->addText('name', _('Name'));
            $attributeContainer->addText('value', _('Value'));
        });
        
//        $button->onClick[] = function(SubmitButton $button) use($attributesContainer) {
//            for($i=0; $i<1; $i++) {
//                $attributeContainer = $attributesContainer->addContainer($i);
//                $attributeContainer->addText('name', _('Name'));
//                $attributeContainer->addText('value', _('Value'));
//            }
//            
//            $button->getForm()->onSuccess = [];
//            $button->getForm()->onPostSuccess = [];
//        };
    }

    
    /** {@inheritDoc} */
	public function setDefaultValues($entity, $langEntity = null)
	{
//        $this['position']->setDefaultValue($entity->getParameter('position'));
	}
    
    public function update($form, $values)
    {
        parent::update($form, $values);
        
        \Tracy\Debugger::barDump($this['attribute'], 'update');
        
        foreach($this->getHttpData()['attribute'] as $attr) {
            $form->getEntity()->setParameter('container', $form->getEntity()->getParameter('container') + [$attr['name'] => $attr['value']]);
        }
    }

}