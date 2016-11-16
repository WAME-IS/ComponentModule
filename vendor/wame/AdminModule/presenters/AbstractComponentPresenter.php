<?php

namespace App\AdminModule\Presenters;

use Wame\ComponentModule\Forms\ComponentForm;
use Wame\ComponentModule\Repositories\PositionRepository;


abstract class AbstractComponentPresenter extends ComponentPresenter
{
	/** @var ComponentForm @inject */
	public $componentForm;

	/** @var PositionRepository @inject */
	public $positionRepository;


    /** renders ***************************************************************/

	public function renderCreate()
	{
		$this->template->siteTitle = _('Create component');
		$this->template->subTitle = $this->getComponentName();
	}


	public function renderEdit()
	{
		$this->template->siteTitle = _('Edit component');
		$this->template->subTitle = $this->getComponentName();
	}


    /** components ************************************************************/

    /**
	 * Shop product component form
	 *
	 * @return ComponentForm
	 */
	protected function createComponentForm()
	{
		$form = $this->componentForm
						->setType($this->getComponentIdentifier())
						->setId($this->id)
						->build();

		return $form;
	}


    /** methods ***************************************************************/

    /**
     * Get component identifier
     */
    protected abstract function getComponentIdentifier();

    /**
     * Get component name
     */
    protected abstract function getComponentName();

}
