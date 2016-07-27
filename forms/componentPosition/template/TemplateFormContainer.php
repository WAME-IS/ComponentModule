<?php

namespace Wame\ComponentModule\Forms\ComponentPosition;

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
		
		$componentInPositionEntity = $object->componentInPositionEntity;
        
        if ($componentInPositionEntity->component->getParameter('template')) {
            $form['template']->setAttribute('placeholder', $componentInPositionEntity->component->getParameter('template'));
        }
		
		if ($componentInPositionEntity->getParameter('template')) {
			$form['template']->setDefaultValue($componentInPositionEntity->getParameter('template'));
		}
	}

}