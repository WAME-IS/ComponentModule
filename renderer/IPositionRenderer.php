<?php

namespace Wame\ComponentModule\Renderer;

interface IPositionRenderer
{
	/**
	 * Provides complete position rendering.
     * 
     * @param \Wame\ComponentModule\Components\PositionControl $positionControl
	 */
	function render($positionControl);

}
