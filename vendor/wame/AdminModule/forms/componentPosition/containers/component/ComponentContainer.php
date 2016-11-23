<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Containers\ComponentPosition;

use Wame\Utils\HttpRequest;
use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\ComponentModule\Repositories\ComponentRepository;


interface IComponentContainerFactory extends IBaseContainer
{
	/** @return ComponentContainer */
	public function create();
}


class ComponentContainer extends BaseContainer
{
    /** @var ComponentRepository */
    private $componentRepository;

    /** @var integer */
    private $id;


    public function __construct(
        \Nette\DI\Container $container,
        HttpRequest $httpRequest,
        ComponentRepository $componentRepository
    ) {
        parent::__construct($container);

        $this->componentRepository = $componentRepository;

        $this->id = $httpRequest->getParameter('id');
    }


    /** {@inheritDoc} */
    public function configure()
	{
		$this->addHidden('component')
                ->setDefaultValue($this->id);
    }


    /** {@inheritDoc} */
	public function setDefaultValues($entity)
	{
        $this['component']->setDefaultValue($entity->getComponent()->getId());
	}


    /** {@inheritDoc} */
    public function create($form, $values)
    {
        $componentEntity = $this->componentRepository->get(['id' => $values['component']]);

        $form->getEntity()->setComponent($componentEntity);
    }


    /** {@inheritDoc} */
    public function update($form, $values)
    {
        $componentEntity = $this->componentRepository->get(['id' => $values['component']]);

        $form->getEntity()->setComponent($componentEntity);
    }

}