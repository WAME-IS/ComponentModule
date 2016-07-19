<?php

namespace Wame\ComponentModule\Paremeters\Readers;

use Wame\ComponentModule\Paremeters\IParameterReader;

/**
 * ConcatParameterReader concats parameters when reading them from multiple sources
 */
class ConcatParameterReader implements IParameterReader
{

    /** @var string */
    private $glue;

    /**
     * 
     * @param string $glue Glue used to concat parameters
     */
    public function __construct($glue)
    {
        $this->glue = $glue;
    }

    public function read($generator)
    {
        $string = null;
        foreach ($generator as $param) {
            if ($param) {
                if ($string) {
                    $string .= $this->glue . $param;
                } else {
                    $string = $param;
                }
            }
        }
        return $string;
    }
}
