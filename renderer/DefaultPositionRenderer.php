<?php

namespace Wame\ComponentModule\Renderer;

use Nette\Application\UI\Control;
use Nette\Utils\Html;
use Tracy\Debugger;
use Wame\ComponentModule\Components\PositionControl;
use Wame\ComponentModule\Paremeters\Readers\ParameterReaders;
use Wame\Core\Components\BaseControl;

class DefaultPositionRenderer
{

    public $defaults = [
        'list' => [
            'tag' => 'div'
        ],
        'listItem' => [
            'tag' => 'div'
        ]
    ];

    /**
     * Provides complete position rendering.
     * 
     * @param PositionControl $positionControl
     * @return string
     */
    function render($positionControl)
    {
        $listContainerDefault = $this->defaults['list'];
        $listContainer = $this->getContainer($positionControl, $listContainerDefault);

        $this->renderContainerStart($listContainer, $positionControl);

        foreach ($positionControl->getComponents() as $component) {

            $listItemContainerDefault = $this->defaults['listItem'];
            $listItemContainer = $this->getContainer($component, $listItemContainerDefault);

            $this->renderContainerStart($listItemContainer, $component);

            if ($component instanceof BaseControl) {
                $component->willRender("render");
            } else {
                $component->render();
            }

            $this->renderContainerEnd($listItemContainer, $component);
        }

        $this->renderContainerEnd($listContainer, $positionControl);
    }

    /**
     * @param Html $container
     * @param Control $control
     */
    private function renderContainerStart($container, $control)
    {
        if ($container) {
            echo $container->startTag();
        } else {
            if (Debugger::isEnabled()) {
                echo '<!-- ' . $control->getUniqueId() . ' -->';
            }
        }
    }

    /**
     * @param Html $container
     * @param Control $control
     */
    private function renderContainerEnd($container, $control)
    {
        if ($container) {
            echo $container->endTag();
        } else {
            if (Debugger::isEnabled()) {
                echo '<!-- end ' . $control->getUniqueId() . ' -->';
            }
        }
    }

    /**
     * Get position Html container
     * 
     * @param BaseControl $control
     * @param array $defaultParams
     * @return Html
     */
    private function getContainer($control, $defaultParams)
    {
        $containerParams = $control->getComponentParameter("container", ParameterReaders::$HTML);
        $containerParams = array_replace_recursive($defaultParams, $containerParams);

        if (array_key_exists('tag', $containerParams) && $tag = $containerParams['tag']) {
            unset($containerParams['tag']);
            return Html::el($tag, $containerParams);
        }
    }
}
