<?php

namespace ComponentModule\Renderer;

use Nette\Utils\Html;
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

        echo $listContainer->startTag();

        foreach ($positionControl->getComponents() as $component) {

            $listItemContainerDefault = $this->defaults['listItem'];
            $listItemContainer = $this->getContainer($component, $listItemContainerDefault);

            echo $listItemContainer->startTag();

            //$control->getComponentParameter("render");

            if($component instanceof BaseControl) {
                $component->willRender("render");
            } else {
                $component->render();
            }

            echo $listItemContainer->endTag();
        }

        echo $listContainer->endTag();
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
        $containerParams = $control->getComponentParameter("container");
        $containerParams = array_replace_recursive($defaultParams, $containerParams);

        if (array_key_exists('tag', $containerParams)) {
            $tag = $containerParams['tag'];
            unset($containerParams['tag']);
        } else {
            throw new \Nette\InvalidArgumentException("Container has to have tag specified.");
        }
        
        return Html::el($tag, $containerParams);
    }
}
