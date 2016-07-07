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


	public function createItem($element, $item) 
	{
		$icon = Html::el('div')->addClass('caption')->setHtml(Html::el('span')->addClass($item->icon . ' fa-4x text-primary'));
		
		$caption = $this->getCaption($item);
		
		$html = Html::el('div')->setClass('thumbnail text-center')->setHtml($icon . $caption);
		
		return $element->addAttributes($item->attributes)->data('name', $item->name)->setHtml($html);
	}
	
	
	private function getCaption($item)
	{
		$title = Html::el('div')->setClass('lead text-center')->setText($item->title);
		
		if ($item->description) {
			$description = $item->description;
		} else {
			$description = '';
		}
        
        $url = new Url($item->link);
        $url->appendQuery(['p' => $this->positionId]);
		
		$button = Html::el('p')->setHtml(Html::el('a')->href($url)->setClass('btn btn-success')->setHtml(
						Html::el('span')->setClass('fa fa-plus') . 
						Html::el()->setText(' ' . _('Add component'))
					));
		
		$html = Html::el('div')->setClass('caption')->setHtml($title . $description . $button);
		
		return $html;
	}
	
}
