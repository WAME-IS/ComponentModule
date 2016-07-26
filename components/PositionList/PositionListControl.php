<?php 

namespace Wame\ComponentModule\Components;

use Wame\Core\Components\BaseControl;
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
	
	
	public function __construct(\Nette\DI\Container $container, PositionRepository $positionRepository) 
	{
        parent::__construct($container);
        
		$this->positionRepository = $positionRepository;
	}
	
	
	public function render()
	{
		$this->template->positions = $this->positionRepository->find(['status !=' => PositionRepository::STATUS_REMOVE]);
	}

}