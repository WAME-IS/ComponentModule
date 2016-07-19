<?php

namespace Wame\ComponentModule\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;

class AdvancedComponentFormContainer extends BaseFormContainer
{
    protected function configure() 
	{		
		$form = $this->getForm();
		
		$form->addGroup(_('Advanced'));
		
        $form->addText('name', _('Component name'));

        $form->addText('class', _('CSS class'));		

		$form->addText('template', _('Template'))
				->setAttribute('placeholder', 'default.latte');
		
        $form->addText('cache', _('Cache'))
				->setDefaultValue(0);		
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$componentEntity = $object->componentEntity;

		$form['name']->setDefaultValue($componentEntity->name);
		
		if ($componentEntity->getParameter('class')) {
			$form['class']->setDefaultValue($componentEntity->getParameter('class'));
		}
		
		if ($componentEntity->getParameter('template')) {
			$form['template']->setDefaultValue($componentEntity->getParameter('template'));
		}
		
		if ($componentEntity->getParameter('cache')) {
			$form['cache']->setDefaultValue($componentEntity->getParameter('cache'));
		}
	}

}