<?php

namespace Wame\ComponentModule\Forms\Position;

use Wame\DynamicObject\Forms\BaseFormContainer;


class BasicFormContainer extends BaseFormContainer
{
    protected function configure()
	{
		$form = $this->getForm();

		$form->addGroup(_('Basic'));

        $form->addText('title', _('Title'))
				->setRequired(_('Please enter title.'));

        $form->addTextArea('description', _('Description'));
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();

		$form['title']->setDefaultValue($object->positionEntity->getTitle());
		$form['description']->setDefaultValue($object->positionEntity->getDescription());
	}

}