<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;

interface ITagContainerFactory extends IBaseContainer
{
	/** @return TagContainer */
	public function create();
}

class TagContainer extends BaseContainer
{
    /** {@inheritDoc} */
    public function configure() 
	{
		$this->addText('tag', _('Tag'))
				->setOption('description', _('e.g.: div, span, ul, article, aside, header, footer, main, nav, section...'));
    }

    
    /** {@inheritDoc} */
	public function setDefaultValues($entity, $langEntity = null)
	{
        $this['tag']->setDefaultValue($entity->getParameter('container')['tag']);
	}

    /** {@inheritDoc} */
    public function create($form, $values)
    {
        $form->getEntity()->setParameter('container', ['tag' => $values['tag']]);
    }
    
    /** {@inheritDoc} */
    public function update($form, $values)
    {
        $form->getEntity()->setParameter('container', ['tag' => $values['tag']]);
    }

}