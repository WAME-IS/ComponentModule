<?php

namespace Wame\ComponentModule\Paremeters\Readers;

use Wame\ComponentModule\Paremeters\IParameterReader;

class DefaultParameterReader implements IParameterReader
{

    public function read($generator)
    {
        $arrayValue = null;
        foreach ($generator as $value) {
            if ($value) {
                if (is_array($value)) {
                    if ($arrayValue === null) {
                        $arrayValue = [];
                    }
                    $arrayValue = array_replace_recursive($arrayValue, $value);
                } else {
                    return $value;
                }
            }
        }
        return $arrayValue;
    }
}
