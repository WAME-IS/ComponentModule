<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Forms\Containers\ComponentPosition;

use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\ComponentModule\Repositories\PositionRepository;


interface IPositionContainerFactory extends IBaseContainer
{
	/** @return PositionContainer */
	public function create();
}


class PositionContainer extends BaseContainer
{
    /** @var PositionRepository */
    private $positionRepository;

    /** @var array */
    private $positionList;


    public function __construct(
        PositionRepository $positionRepository
    ) {
        parent::__construct();

        $this->positionRepository = $positionRepository;
        $this->positionList = $positionRepository->findPairs(['inList' => PositionRepository::SHOW_IN_LIST], 'name', ['name' => 'ASC']);
    }


    /** {@inheritDoc} */
    public function configure()
	{
		$this->addSelect('position', _('Position'), $this->positionList)
                ->setPrompt('- ' . _('Select position') . ' -');
    }


    /** {@inheritDoc} */
	public function setDefaultValues($entity)
	{
        $this['position']->setDefaultValue($entity->getPosition()->getId());
	}


    /** {@inheritDoc} */
    public function create($form, $values)
    {
        $positionEntity = $this->positionRepository->get(['id' => $values['position']]);

        $form->getEntity()->setPosition($positionEntity);
    }


    /** {@inheritDoc} */
    public function update($form, $values)
    {
        $positionEntity = $this->positionRepository->get(['id' => $values['position']]);

        $form->getEntity()->setPosition($positionEntity);
    }

}