<?php

namespace Wame\ComponentModule\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;

class AdvancedPositionFormContainer extends BaseFormContainer
{
    protected function configure() 
	{		
		$form = $this->getForm();
		
		$form->addGroup(_('Advanced'));
		
        $form->addText('name', _('Position name'));

        $form->addText('class', _('CSS class'));		

        $form->addText('template', _('Template'))
				->setAttribute('placeholder', 'default.latte');
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$positionEntity = $object->positionEntity;

		$form['name']->setDefaultValue($positionEntity->name);
		
		if ($positionEntity->getParameter('class')) {
			$form['class']->setDefaultValue($positionEntity->getParameter('class'));
		}
		
		if ($positionEntity->getParameter('template')) {
			$form['template']->setDefaultValue($positionEntity->getParameter('template'));
		}
	}

}