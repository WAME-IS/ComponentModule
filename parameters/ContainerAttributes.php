<?php

namespace Wame\ComponentModule\Paremeters;


class ContainerAttributes
{
    /**
     * Convert container attributes to database
     * 
     * @param array $attributes
     * @return array
     */
    public static function toDatabase($attributes)
    {
        $return = [];
        
        foreach ($attributes as $attribute) {
            $return[$attribute['name']] = $attribute['value'];
        }
        
        return $return;
    }
    
    
    /**
     * Convert container attributes from database
     * 
     * @param array $attributes
     * @return array
     */
    public static function fromDatabase($attributes)
    {
        $return = [];
        
        foreach ($attributes as $name => $value) {
            $return[] = [
                'name' => $name,
                'value' => $value
            ];
        }
        
        return $return;
    }

}
