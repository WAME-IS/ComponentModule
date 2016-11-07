<?php

namespace Wame\ComponentModule\Vendor\Wame\RouterModule\Subscribers;

use Kdyby\Events\Subscriber;
use Wame\RouterModule\Event\RoutePreprocessEvent;

use Wame\ComponentModule\Repositories\PositionUsageRepository;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;

class RouteListener implements Subscriber
{
    /** @var PositionUsageRepository */
    private $positionUsageRepository;
    
    /** @var ComponentInPositionRepository */
    private $componentInPositionRepository;
    
    
    public function __construct(
        PositionUsageRepository $positionUsageRepository, 
        ComponentInPositionRepository $componentInPositionRepository
    ) {
        $this->positionUsageRepository = $positionUsageRepository;
        $this->componentInPositionRepository = $componentInPositionRepository;
    }
    
    
    public function getSubscribedEvents()
    {
        return ['Wame\RouterModule\Routers\Router::onPreprocess'];
    }
    
    public function onPreprocess(RoutePreprocessEvent $event)
    {
//        $entity = $event->getRoute();
//        
//        $usages = $this->positionUsageRepository->find([
//            'presenter' => "{$entity->module}:{$entity->presenter}", 
//            'component !=' => ''
//        ]);
//
//        $before = $entity->route;
//
//        foreach($usages as $usage) {
//                $componentsInPosition = $this->componentInPositionRepository->find([
//                    'position' => $usage->position,
//                    'component' => 118
//                ]);
//
//                foreach($componentsInPosition as $cip) {
//                    \Tracy\Debugger::barDump($cip->component);
//                    if($cip->component->status) {
//                        $entity->route .= "[/{$cip->id}-from=<{$usage->component}-positionFilters-{$cip->component->name}-from>]";
//                        $entity->route .= "[/{$cip->id}-to=<{$usage->component}-positionFilters-{$cip->component->name}-to>]";
//                    }
//                }
//        }
//
//        \Tracy\Debugger::barDump($entity->route, $before, [
//            \Tracy\Dumper::TRUNCATE => false
//        ]);
    }
    
}
