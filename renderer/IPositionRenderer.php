<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ComponentModule\Renderer;

/**
 *
 * @author Ienze
 */
interface IPositionRenderer
{
	/**
	 * Provides complete position rendering.
     * 
     * @param \Wame\ComponentModule\Components\PositionControl $positionControl
	 * @return string
	 */
	function render($positionControl);

}
