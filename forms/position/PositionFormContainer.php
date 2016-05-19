<?php

namespace Wame\ComponentModule\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;

interface IPositionFormContainerFactory
{
	/** @return PositionFormContainer */
	function create();
}


class PositionFormContainer extends BaseFormContainer
{
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

	
    protected function configure() 
	{		
		$form = $this->getForm();

        $form->addText('class', _('CSS class'));		

		$form->addText('template', _('Template'));
		
        $form->addText('cache', _('Cache'));		
    }
	
	
	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$componentInPositionEntity = $object->componentInPositionEntity;
		
		if ($componentInPositionEntity->component->getParameter('template')) {
			$componentTemplate = $componentInPositionEntity->component->getParameter('template');
		} else {
			$componentTemplate = \App\Core\Components\BaseControl::DEFAULT_TEMPLATE;
		}
		
		$form['template']->setAttribute('placeholder', $componentTemplate);
		
		$form['class']->setAttribute('placeholder', $componentInPositionEntity->component->getParameter('class'));
		
		$form['cache']->setAttribute('placeholder', $componentInPositionEntity->component->getParameter('cache'));
		
		if ($componentInPositionEntity->getParameter('class')) {
			$form['class']->setDefaultValue($componentInPositionEntity->getParameter('class'));
		}
		
		if ($componentInPositionEntity->getParameter('template')) {
			$form['template']->setDefaultValue($componentInPositionEntity->getParameter('template'));
		}
		
		if ($componentInPositionEntity->getParameter('cache')) {
			$form['cache']->setDefaultValue($componentInPositionEntity->getParameter('cache'));
		}
	}
	
}