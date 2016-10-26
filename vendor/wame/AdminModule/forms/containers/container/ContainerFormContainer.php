<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Containers;

use Nette\Forms\Container;
use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Groups\ContainerGroup;
use Wame\ComponentModule\Paremeters\ContainerAttributes;


interface IContainerContainerFactory extends IBaseContainer
{
	/** @return ContainerContainer */
	public function create();
}


class ContainerContainer extends BaseContainer
{
    /** {@inheritDoc} */
    public function configure()
	{
        $this->getForm()->addBaseGroup(new ContainerGroup, 'ContainerGroup');

        $this->addText('tag', _('Tag'))
                    ->setOption('description', _('e.g.: div, span, ul, a, article, aside, header, footer, main, nav, section...'));

		$attributes = $this->addDynamic('container', function (Container $container)
        {
            $container->addText('name', _('Name'));

            $container->addText('value', _('Value'));

            $container->addSubmit('remove', _('Remove'))
                        ->setAttribute('class', 'btn btn-danger btn-xs')
                        ->setValidationScope(false)
                        ->addRemoveOnClick();
        });

        $attributes->addSubmit('add', _('Add attribute'))
                    ->setOption('description', _('Attributes for container wrapper position.'))
                    ->setAttribute('class', 'btn btn-default btn-xs')
                    ->setValidationScope(false)
                    ->addCreateOnClick(true);
    }


    /** {@inheritDoc} */
	public function setDefaultValues($entity)
	{
        if ($entity->getParameter('container')) {
			$this['container']->setDefaults(ContainerAttributes::fromDatabase($entity->getParameter('container')));
		}
	}


    /** {@inheritDoc} */
    public function create($form, $values)
    {
        $entity = $form->getEntity();
        $parameters = $entity->getParameters();
        $container = ['container' => ContainerAttributes::toDatabase($values['container'])];

        $entity->setParameters(array_replace($parameters, $container));
    }


    /** {@inheritDoc} */
    public function update($form, $values)
    {
        $entity = $form->getEntity();
        $parameters = $entity->getParameters();
        $container = ['container' => ContainerAttributes::toDatabase($values['container'])];

        $entity->setParameters(array_replace($parameters, $container));
    }

}
