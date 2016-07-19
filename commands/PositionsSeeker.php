<?php

namespace Wame\ComponentModule\Commands;

class PositionsSeeker extends \Nette\Object
{
    
    /** @var \Nette\DI\Container */
    private $container;
    
    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
    }
    
    public function seek()
    {
        $positions = [];
        
        $presenters = $this->container->findByType(\Nette\Application\UI\Presenter::class);
        dump($presenters);
        exit();
        
        return $positions;
    }
    
}
