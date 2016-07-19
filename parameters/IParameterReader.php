<?php

namespace Wame\ComponentModule\Paremeters;

use Generator;

interface IParameterReader
{

    /**
     * Reader used to read values from ParameterSources
     * 
     * @param Generator $generator
     */
    public function read($generator);
}
