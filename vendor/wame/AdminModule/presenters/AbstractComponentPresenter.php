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


    /** actions ***************************************************************/

    public function actionCreate()
    {
        if ($this->getParameter('p')) {
            $position = $this->positionRepository->get(['id' => $this->getParameter('p')]);

            if (!$position) {
                $this->flashMessage(_('This position does not exist.'), 'danger');
                $this->redirect(':Admin:Component:', ['id' => null]);
            }

            if ($position->getStatus() == PositionRepository::STATUS_REMOVE) {
                $this->flashMessage(_('This position is removed.'), 'danger');
                $this->redirect(':Admin:Component:', ['id' => null]);
            }

            if ($position->getStatus() == PositionRepository::STATUS_DISABLED) {
                $this->flashMessage(_('This position is disabled.'), 'warning');
            }
        }
    }


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
	 * Form component
	 *
	 * @return ComponentForm
	 */
	protected function createComponentForm()
	{
		$form = $this->componentForm
						->setType($this->getComponentIdentifier())
						->setId($this->id);

        if (count($this->formContainers) > 0) {
            foreach ($this->formContainers as $container) {
                $form->addFormContainer($container['service'], $container['name'], $container['priority']);
            }
        }

		return $form->build();
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
