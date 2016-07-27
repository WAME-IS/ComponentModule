<?php

namespace Wame\ComponentModule\Forms;

use Nette\Forms\Container;
use Wame\DynamicObject\Forms\BaseFormContainer;
use Wame\ComponentModule\Paremeters\ContainerAttributes;


class ContainerComponentFormContainer extends BaseFormContainer
{
    protected function configure() 
	{		
		$form = $this->getForm();
        
        $form->addGroup(_('Container'))->setOption('description', _('You can add attributes for container that component wrapper.'));
        
        $attributes = $form->addDynamic('container', function (Container $container) 
        {
            $container->addText('name', _('Name'));
            
            $container->addText('value', _('Value'));
            
            $container->addSubmit('remove', _('Remove'))
                        ->setAttribute('class', 'btn btn-danger btn-xs')
                        ->setValidationScope(false)
                        ->addRemoveOnClick();
        });

        $add = $attributes->addSubmit('add', _('Add attribute'))
                    ->setAttribute('class', 'btn btn-default')
                    ->setValidationScope(false)
                    ->addCreateOnClick(true);
        
        $form->getCurrentGroup()->add($add);
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$componentEntity = $object->componentEntity;

		if ($componentEntity->getParameter('container')) {
			$form['container']->setDefaults(ContainerAttributes::fromDatabase($componentEntity->getParameter('container')));
		}
	}

}