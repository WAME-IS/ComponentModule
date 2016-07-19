<?php

namespace ComponentModule\Renderer;

use Nette\Utils\Html;
use Wame\ComponentModule\Components\PositionControl;
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
        $listContainerDefault['data-position'] = $positionControl->getPositionName();
        $listContainer = $this->getContainer($positionControl, $listContainerDefault);

        echo $listContainer->startTag();

        foreach ($positionControl->getComponents() as $component) {

            $listItemContainerDefault = $this->defaults['listItem'];
            $listItemContainer = $this->getContainer($positionControl, $listItemContainerDefault);

            echo $listItemContainer->startTag();

            //$control->getComponentParameter("render");

            $component->render();

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
        $containerParams = $defaultParams;
//        dump($control->getComponentParameter("container", ParameterReaders::$HTML));
//        exit();
//        $containerParams = array_replace_recursive($defaultParams, );

        if (array_key_exists('tag', $containerParams)) {
            $tag = $containerParams['tag'];
            unset($containerParams['tag']);
        }

        return Html::el($tag, $containerParams);
    }
}
