<?php

namespace Wame\ComponentModule\Renderer;

use Nette\Application\UI\Control;
use Nette\Utils\Html;
use Tracy\Debugger;
use Wame\ComponentModule\Components\PositionControl;
use Wame\ComponentModule\Paremeters\Readers\ParameterReaders;
use Wame\Core\Components\BaseControl;

class PositionRenderer extends \Wame\ListControl\Renderer\SimpleListRenderer
{
    private $renderingComponent;

    protected function renderComponent($component)
    {
        $this->renderingComponent = $component;
        parent::renderComponent($component);
    }

    /**
     * @param Html $container
     * @param Control $control
     */
    private function renderContainerStart($container)
    {
        if ($container) {
            echo $container->startTag();
        } else {
            if (Debugger::isEnabled() && $this->renderingComponent instanceof BaseControl) {
                echo '<!-- ' . $this->renderingComponent->getUniqueId() . ' -->';
            }
        }
    }

    /**
     * @param Html $container
     * @param Control $control
     */
    private function renderContainerEnd($container)
    {
        if ($container) {
            echo $container->endTag();
        } else {
            if (Debugger::isEnabled()) {
                echo '<!-- end ' . $this->renderingComponent->getUniqueId() . ' -->';
            }
        }
    }
}
