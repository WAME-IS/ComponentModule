<?php

namespace Wame\ComponentModule\Vendor\Wame\MenuModule\Components\ComponentMenu;

use Nette\Application\IRouter;
use Nette\Http\Request;
use Nette\Utils\Html;
use Nette\Http\Url;

class ItemTemplate extends \Nette\Object
{
	/** @var integer */
	private $positionId;
	
	
	public function __construct(IRouter $router, Request $httpRequest) 
	{
		$this->positionId = $router->match($httpRequest)->getParameter('id');
	}


	public function createItem($element, \Wame\ComponentModule\Registers\IComponent $item) 
	{
        if ($item->getLinkCreate() != null) {
            $icon = Html::el('div')->addClass('caption')->setHtml(Html::el('span')->addClass($item->getIcon() . ' fa-4x text-primary'));

            $caption = $this->getCaption($item);

            $html = Html::el('div')->setClass('thumbnail text-center')->setHtml($icon . $caption);
		
            $return = $element->data('name', $item->getName())->setHtml($html);
        } else {
            $return = Html::el();
        }
        
        return $return;
	}
	
	private function getCaption($item)
	{
		$title = Html::el('div')->setClass('lead text-center')->setText($item->getTitle());
		
		if ($item->getDescription()) {
			$description = $item->getDescription();
		} else {
			$description = '';
		}
        
        $url = new Url($item->getLinkCreate());
        $url->appendQuery(['p' => $this->positionId]);
		
		$button = Html::el('p')->setHtml(Html::el('a')->href($url)->setClass('btn btn-success')->setHtml(
						Html::el('span')->setClass('fa fa-plus') . 
						Html::el()->setText(' ' . _('Add component'))
					));
		
		$html = Html::el('div')->setClass('caption')->setHtml($title . $description . $button);
		
		return $html;
	}
	
}
