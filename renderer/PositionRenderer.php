<?php

namespace Wame\ComponentModule\Renderer;

use Nette\Application\UI\Control;
use Nette\Utils\Html;
use Tracy\Debugger;
use Wame\Core\Components\BaseControl;
use Wame\ListControl\Renderer\SimpleListRenderer;

class PositionRenderer extends SimpleListRenderer
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
