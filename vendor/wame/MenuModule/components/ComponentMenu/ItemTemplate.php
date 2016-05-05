<?php

namespace Wame\ComponentModule\Vendor\Wame\MenuModule\Components\ComponentMenu;

use Nette\Utils\Html;

class ItemTemplate extends \Nette\Object
{	
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
		
		$button = Html::el('p')->setHtml(Html::el('a')->href($item->link)->setClass('btn btn-success')->setHtml(
						Html::el('span')->setClass('fa fa-plus') . 
						Html::el()->setText(' ' . _('Add component'))
					));
		
		$html = Html::el('div')->setClass('caption')->setHtml($title . $description . $button);
		
		return $html;
	}
	
}
