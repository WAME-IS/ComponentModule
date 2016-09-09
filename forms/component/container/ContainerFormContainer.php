<?php

namespace Wame\ComponentModule\Forms\Component;

use Nette\Forms\Container;
use Wame\DynamicObject\Forms\BaseFormContainer;
use Wame\ComponentModule\Paremeters\ContainerAttributes;


class ContainerFormContainer extends BaseFormContainer
{
    protected function configure()
    {
		$form = $this->getForm();
        
        $form->addGroup(_('Container'));
        
        $attributes = $form->addDynamic('container', function (Container $container) 
        {
            $container->addText('name', _('Name'));
            
            $container->addText('value', _('Value'));
            
            $container->addSubmit('remove', _('Remove'))
                        ->setAttribute('class', 'btn btn-danger btn-xs')
                        ->setValidationScope(false)
                        ->addRemoveOnClick();
        });
        
        $tag = $attributes->addText('tag', _('Tag'))
                    ->setAttribute('placeholder', 'div')
                    ->setOption('description', _('e.g.: div, span, ul, article, aside, header, footer, main, nav, section...'));
        
        $form->getCurrentGroup()->add($tag);

        $add = $attributes->addSubmit('add', _('Add attribute'))
                    ->setOption('description', _('Attributes for container wrapper component.'))
                    ->setAttribute('class', 'btn btn-default')
                    ->setValidationScope(false)
                    ->addCreateOnClick(true);
        
        $form->getCurrentGroup()->add($add);
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
        if (isset($object->componentEntity)) {
            $componentEntity = $object->componentEntity;
        } elseif (isset($object->componentInPositionEntity)) {
            $componentEntity = $object->componentInPositionEntity->getComponent();
        }

		if ($componentEntity->getParameter('container')) {
			$form['container']->setDefaults(ContainerAttributes::fromDatabase($componentEntity->getParameter('container')));
		}
	}

}