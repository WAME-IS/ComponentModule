<?php

namespace Wame\ComponentModule\Paremeters;

use Generator;
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
        
        return $this->getParameterByReader($generator(), $parameterReader);
    }

    /**
     * 
     * @param Generator $generator
     * @param callable $parameterReader
     * @throws InvalidArgumentException
     */
    private function getParameterByReader($generator, $parameterReader = null)
    {
        if (!$parameterReader) {
            $parameterReader = $this->defaultParameterReader;
        }

        if (is_array($parameterReader)) {
            //default values
            $out = [];

            $values = iterator_to_array($generator);
            
            $keys = [];
            foreach ($values as $value) {
                if (!is_array($value)) {
                    throw new InvalidArgumentException("This parameter has to be array, because reader requires it.");
                }
                foreach ($value as $key => $value) {
                    if (!in_array($key, $keys)) {
                        $keys[] = $key;
                    }
                }
            }
            
            foreach ($keys as $key) {
                $subParameterReader = null;
                if (isset($parameterReader[$key])) {
                    $subParameterReader = $parameterReader[$key];
                }

                $subGenerator = function() use ($values, $key, $out) {
                    foreach ($values as $value) {
                        if (isset($value[$key])) {
                            yield $value[$key];
                        }
                    }
                };
                
                $out[$key] = $this->getParameterByReader($subGenerator(), $subParameterReader);
            }

            return $out;
        } else {
            return $parameterReader->read($generator);
        }
    }
}
