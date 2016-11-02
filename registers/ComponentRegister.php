<?php

namespace Wame\ComponentModule\Registers;

use Wame\Core\Registers\PriorityRegister;
use Wame\MenuModule\Models\IMenuProvider;

class ComponentRegister extends PriorityRegister implements IMenuProvider
{

    public function __construct()
    {
        parent::__construct(IComponent::class);
    }

    protected function getDefaultName($service)
    {
        return \Wame\Utils\Strings::getClassName(get_class($service));
    }

    /**
     * Get items from services
     *
     * @return array
     */
    public function getItems()
    {//TODO remove, with IMenuProvider
        return $this->getAll();
    }

    public function getList()
    {
        $return = [];

        foreach($this->array as $item) {
            $return[$item['name']] = $item['service'];
        }

        ksort($return);

        return $return;
    }

}
