<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Components;

use Nette\DI\Container;
use Wame\AdminModule\Components\BaseControl;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Repositories\PositionRepository;

interface IPositionListControlFactory
{
	/** @return PositionListControl */
	public function create();
}

class PositionListControl extends BaseControl
{
	/** @var PositionRepository */
	private $positionRepository;

	/** @var PositionEntity[] */
	private $positions;


	public function __construct(Container $container, PositionRepository $positionRepository)
	{
        parent::__construct($container);

		$this->positionRepository = $positionRepository;

		// TODO: pre kazdu component relaciu vyvolava samostatny request
        $this->positions = $this->positionRepository->find([
            'status !=' => PositionRepository::STATUS_REMOVE, // TODO: tento riadok nemusi byt, lebo doctrine filters
            'inList' => PositionRepository::SHOW_IN_LIST
        ]);
	}


    /** rendering *************************************************************/

    public function render()
	{
		$this->template->positions = $this->positions;
	}

}
