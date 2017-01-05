<?php

namespace Wame\ComponentModule\Helpers;

use Nette\Utils\Html;
use Wame\ComponentModule\Paremeters\Readers\ParameterReaders;
use Wame\Core\Components\BaseControl;

class Helpers
{
    /**
     * Get HTML container
     * 
     * @param BaseControl $control
     * @param array $defaultParams
     * @param string $paramName
     * @return Html
     */
    public static function getContainer($control, $defaultParams, $paramName)
    {
        $containerParams = $control->getComponentParameter($paramName, ParameterReaders::$HTML);
        $containerParams = array_replace_recursive($defaultParams, $containerParams);

        if (array_key_exists('tag', $containerParams) && $tag = $containerParams['tag']) {
            unset($containerParams['tag']);
            return Html::el($tag, $containerParams);
        }
    }

    /**
     * @param Html $container
     */
    public static function renderContainerStart($container)
    {
        if ($container) {
            echo $container->startTag();
        }
    }

    /**
     * @param Html $container
     */
    public static function renderContainerEnd($container)
    {
        if ($container) {
            echo $container->endTag();
        }
    }

}
