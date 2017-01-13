<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Components;

use Nette\DI\Container;
use Tracy\Debugger;
use Wame\AdminModule\Components\BaseControl;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\Utils\HttpRequest;

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

    /** @var ComponentInPositionEntity[] */
    private $componentPositionList;


    public function __construct(
        Container $container,
        ComponentRepository $componentRepository,
        ComponentInPositionRepository $componentInPositionRepository,
        HttpRequest $httpRequest
    ) {
        parent::__construct($container);

        $this->componentRepository = $componentRepository;
        $this->componentInPositionRepository = $componentInPositionRepository;

        // TODO: zistit ci sa neda nahradit za $this->getPresenter()->id; v beforeRender metode
        $this->id = $httpRequest->getParameter('id');

        $component = $this->componentRepository->get(['id' => $this->id]);
        $this->componentPositionList = $this->componentInPositionRepository->find(['component' => $component]);
    }


    /** rendering *************************************************************/

    public function render()
    {
        $this->template->componentPositionList = $this->componentPositionList;
    }

}
