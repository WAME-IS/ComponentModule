<?php

namespace Wame\ComponentModule\Paremeters;

interface IParameterSource
{

    /**
     * Get parameter
     * 
     * @param string $parameter
     * @return mixed
     */
    public function getParameter($parameter);
}
