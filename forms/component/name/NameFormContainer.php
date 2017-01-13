<?php

namespace Wame\ComponentModule\Forms\Component;

use Wame\DynamicObject\Forms\BaseFormContainer;


class NameFormContainer extends BaseFormContainer
{
    protected function configure() 
	{		
		$form = $this->getForm();
        
        $form->addText('name', _('Component name'));
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();

		$componentEntity = $object->componentEntity;

		$form['name']->setDefaultValue($componentEntity->name);
	}

}