<?php

namespace Wame\ComponentModule\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;

class PositionNameFormContainer extends BaseFormContainer
{
    protected function configure() 
	{		
		$form = $this->getForm();
		
        $form->addText('name', _('Position name'));
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$positionEntity = $object->positionEntity;

		$form['name']->setDefaultValue($positionEntity->name);
	}

}