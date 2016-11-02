<?php

namespace Wame\ComponentModule\Components;

use Wame\Core\Components\BaseControl;
use Wame\ComponentModule\Registers\ComponentRegister;


interface IAddComponentControlFactory
{
	/** @return AddComponentControl */
	public function create();
}


class AddComponentControl extends BaseControl
{
	/** @var ComponentRegister */
	private $componentRegister;


	public function __construct(
        \Nette\DI\Container $container,
        ComponentRegister $componentRegister
    ) {
        parent::__construct($container);

		$this->componentRegister = $componentRegister->getList();
	}


	public function render()
	{
        $component = $this->getParameter('c');

        if ($component && isset($this->componentRegister[$component])) {
            $component = $this->componentRegister[$component];
        }

		$this->template->componentList = $this->componentRegister;
		$this->template->info = $component;
	}

    public function handleRedraw()
    {
        $component = $this->getParameter('c');

        if ($component && isset($this->componentRegister[$component])) {
            $component = $this->componentRegister[$component];
        }

        $this->template->info = $component;

        $this->redrawControl();
    }

}
