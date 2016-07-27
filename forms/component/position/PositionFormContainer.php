<?php

namespace Wame\ComponentModule\Forms\Component;

use Nette\Application\IRouter;
use Nette\Http\Request;
use Wame\DynamicObject\Forms\BaseFormContainer;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;


interface IPositionFormContainerFactory
{
	/** @return PositionFormContainer */
	function create();
}


class PositionFormContainer extends BaseFormContainer
{
	/** @var ComponentInPositionRepository */
	public $componentInPositionRepository;
	
	/** @var array */
	private $positionList = [];
	
	/** @var integer */
	private $position;


	public function __construct(
		IRouter $router, 
		Request $httpRequest, 
		PositionRepository $positionRepository,
		ComponentInPositionRepository $componentInPositionRepository
	) {
		parent::__construct();
		
		$this->componentInPositionRepository = $componentInPositionRepository;
		$this->position = $router->match($httpRequest)->getParameter('p');
		$this->positionList = $positionRepository->findPairs(['status' => PositionRepository::STATUS_ENABLED], 'name', ['name']);
		
		if ($this->position && !array_key_exists($this->position, $this->positionList)) {
			$position = $positionRepository->get(['id' => $this->position]);
			$this->positionList[$this->position] = $position->name . ' [' . _('Disabled') . ']';
		}
	}


    protected function configure() 
	{		
		$form = $this->getForm();
		
		$form->addMultiSelect('position', _('Position'), $this->positionList)
				->setDefaultValue($this->position);
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();

		$positions = $this->componentInPositionRepository->getPositions($object->componentEntity);

		if (count($positions) > 0) {
			$form['position']->setDefaultValue(array_keys($positions));
		}
	}

}