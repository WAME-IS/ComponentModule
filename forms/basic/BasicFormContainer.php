<?php

namespace Wame\ComponentModule\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;

interface IBasicFormContainerFactory
{
	/** @return AdvancedFormContainer */
	function create();
}


class BasicFormContainer extends BaseFormContainer
{	
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

	
    protected function configure() 
	{		
		$form = $this->getForm();
		
		$form->addGroup(_('Basic'));

        $form->addText('title', _('Title'))
				->setRequired(_('Please enter title.'));
		
        $form->addTextArea('description', _('Description'));		
    }
	
	
//	public function setDefaultValues($object)
//	{
//		$form = $this->getForm();
//		
//		$address = $this->addressRepository->get(['user' => $object->id]);
//
//		if ($address) {
//			$form['street']->setDefaultValue($address->street);
//			$form['houseNumber']->setDefaultValue($address->houseNumber);
//			$form['zipCode']->setDefaultValue($address->zipCode);
//			$form['city']->setDefaultValue($address->city);
//		}
//	}
	
}