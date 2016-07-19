<?php

namespace Wame\ComponentModule\Paremeters;

use Nette\InvalidArgumentException;
use Wame\ComponentModule\Paremeters\Readers\DefaultParameterReader;
use Wame\Core\Registers\PriorityRegister;

class ParametersCombiner extends PriorityRegister implements IParameterSource
{

    /** @var DefaultParameterReader */
    private $defaultParameterReader;

    public function __construct()
    {
        parent::__construct(IParameterSource::class);

        $this->defaultParameterReader = new DefaultParameterReader();
    }

    /**
     * Get parameter by name
     * 
     * In case special processing of result is required $parameterReader can be used.
     * 
     * @param string $parameter
     * @param callable $parameterReader
     * @return mixed
     */
    public function getParameter($parameter, $parameterReader = null)
    {
        $parameterSources = $this->getAll();
        $generator = function() use ($parameterSources, $parameter) {
            foreach ($parameterSources as $parameterSource) {
                $value = $parameterSource->getParameter($parameter);
                if ($value) {
                    yield $value;
                }
            }
        };

        return $this->getParameterByReader($generator, $parameterReader);
    }

    /**
     * 
     * @param Generator $generator
     * @param callable $parameterReader
     * @throws InvalidArgumentException
     */
    private function getParameterByReader($generator, $parameterReader)
    {
        if (!$parameterReader) {
            $parameterReader = $this->defaultParameterReader;
        }

        if (is_array($parameterReader)) {
            //TODO
            /*
              if(is_array($value)) {
              $this->
              } else {
              throw new Nette\InvalidArgumentException("Parameter is not an array but readers are passed in array.");
              }

             */
        } else {
            return $parameterReader->read($generator);
        }
    }
}
