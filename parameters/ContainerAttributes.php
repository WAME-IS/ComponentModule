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
        
        $attr = $attributes;
        
        if (isset($attr['tag'])) {
            $return['tag'] = $attr['tag'];
            unset($attr['tag']);
        }
        
        if ($return['tag'] == '') {
            unset($return['tag']);
        }
        
        foreach ($attr as $attribute) {
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
        
        $attr = $attributes;
        
        if (isset($attr['tag'])) {
            $return['tag'] = $attr['tag'];
            unset($attr['tag']);
        }
        
        foreach ($attr as $name => $value) {
            $return[] = [
                'name' => $name,
                'value' => $value
            ];
        }
        
        return $return;
    }

}
