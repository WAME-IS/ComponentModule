<?php

namespace ComponentModule\Renderer;

class TemplatePositionRenderer
{
    
    private function prepareComponent($componentInPosition)
	{
		$component = new stdClass();
		$component->type = $componentInPosition->component->type;
		$component->cache = $this->getCache($componentInPosition);
		$component->tags = [$componentInPosition->component->cacheTag];
		
		return $component;
	}
    /**
	 * Provides complete position rendering.
     * 
     * @param \Wame\ComponentModule\Components\PositionControl $positionControl
	 * @return string
	 */
	function render($positionControl) {
        /*
                if ($this->position->getParameter('template')) {
            $this->setTemplateFile($this->position->getParameter('template'));
        } else {
            $this->setTemplateFile(null);
        }

        $this->template->position = $this->position;
        $this->template->container = $this->getContainer();
        $this->template->components = $this->componentList;
        $this->template->lang = $this->lang;

        $this->getTemplateFile();
        $this->template->render();
         
         */

    }
    
}
