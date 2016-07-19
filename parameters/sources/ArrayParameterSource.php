<?php

namespace Wame\ComponentModule\Paremeters;

class ArrayParameterSource implements IParameterSource
{

    /** @var array */
    private $array;

    public function __construct($array)
    {
        $this->array = $array;
    }

    /**
     * Get parameter
     * 
     * @param string $parameter
     * @return mixed
     */
    public function getParameter($parameter)
    {
        if (isset($this->array[$parameter])) {
            return $this->array[$parameter];
        }
    }
}
