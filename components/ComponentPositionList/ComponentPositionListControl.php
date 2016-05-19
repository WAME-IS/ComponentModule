<?php 

namespace Wame\ComponentModule\Components;

use Nette\Application\IRouter;
use Nette\Http\Request;
use App\AdminModule\Components\BaseControl;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;

interface IComponentPositionListControlFactory
{
	/** @return ComponentPositionListControl */
	public function create();	
}


class ComponentPositionListControl extends BaseControl
{	
	/** @var ComponentRepository */
	private $componentRepository;
	
	/** @var ComponentInPositionRepository */
	private $componentInPositionRepository;
	
	/** @var integer */
	private $id;
	
	/** @var string */
	private $lang;
	
	
	public function __construct(
		IRouter $router, 
		Request $httpRequest,
		ComponentRepository $componentRepository,
		ComponentInPositionRepository $componentInPositionRepository
	) {
		parent::__construct();
		
		$this->componentRepository = $componentRepository;
		$this->componentInPositionRepository = $componentInPositionRepository;
		
		$this->id = $router->match($httpRequest)->getParameter('id');
		$this->lang = $componentInPositionRepository->lang;
	}
	
	
	public function render()
	{
		$component = $this->componentRepository->get(['id' => $this->id]);
		
		$this->template->componentPositionList = $this->componentInPositionRepository->find(['component' => $component]);
		$this->template->lang = $this->lang;
		
		$this->getTemplateFile();
		$this->template->render();
	}

}