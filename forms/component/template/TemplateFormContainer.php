<?php

namespace Wame\ComponentModule\Forms\Component;

use Wame\DynamicObject\Forms\BaseFormContainer;


class TemplateFormContainer extends BaseFormContainer
{
    protected function configure() 
	{		
		$form = $this->getForm();
        
		$form->addText('template', _('Template'))
				->setAttribute('placeholder', 'default.latte');
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		if (isset($object->componentEntity)) {
            $componentEntity = $object->componentEntity;
        } elseif (isset($object->componentInPositionEntity)) {
            $componentEntity = $object->componentInPositionEntity->getComponent();
        }
		
		if ($componentEntity->getParameter('template')) {
			$form['template']->setDefaultValue($componentEntity->getParameter('template'));
		}
	}

}