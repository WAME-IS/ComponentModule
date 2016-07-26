<?php

namespace Wame\ComponentModule\Components;

use Nette\Application\IRouter;
use Nette\DI\Container;
use Nette\Http\Request;
use Wame\AdminModule\Components\BaseControl;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Repositories\ComponentRepository;

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
    
    public function __construct(
    Container $container, IRouter $router, Request $httpRequest, ComponentRepository $componentRepository, ComponentInPositionRepository $componentInPositionRepository
    )
    {
        parent::__construct($container);

        $this->componentRepository = $componentRepository;
        $this->componentInPositionRepository = $componentInPositionRepository;

        $this->id = $router->match($httpRequest)->getParameter('id');
    }

    public function render()
    {
        $component = $this->componentRepository->get(['id' => $this->id]);

        $this->template->componentPositionList = $this->componentInPositionRepository->find(['component' => $component]);
    }
}
