<?php

namespace Wame\ComponentModule\Commands\Seeker;

use Nette\Application\IPresenterFactory;
use Nette\Application\PresenterFactory;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Nette\Loaders\RobotLoader;
use Nette\Object;
use function dump;

class PositionsSeeker extends Object
{

    /** @var RobotLoader */
    private $robotLoader;
    
    /** @var PresenterFactory */
    private $presenterFactory;
    
    public function __construct(Container $container, IPresenterFactory $presenterFactory)
    {
        $this->robotLoader = $container->getService('robotLoader');
        $this->presenterFactory = $presenterFactory;
    }

    public function seek()
    {
        $classes = $this->robotLoader->getIndexedClasses();
        foreach ($classes as $class => $classFile) {
            $parents = @class_parents($class);
            if(in_array(Presenter::class, $parents)) {
                $this->seekInPresenter($class);
            }
        }
    }
    
    private function seekInPresenter($presenterClass)
    {
        $name = $this->presenterFactory->unformatPresenterClass($presenterClass);
        dump($name);
    }
}
