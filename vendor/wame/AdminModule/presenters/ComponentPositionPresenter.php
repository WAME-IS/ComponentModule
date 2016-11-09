<?php

namespace App\AdminModule\Presenters;

use Wame\DynamicObject\Vendor\Wame\AdminModule\Presenters\AdminFormPresenter;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\ComponentModule\Forms\ComponentPositionForm;
use Wame\ComponentModule\Registers\ComponentRegister;


class ComponentPositionPresenter extends AdminFormPresenter
{
	/** @var ComponentInPositionRepository @inject */
	public $repository;

	/** @var ComponentRepository @inject */
	public $componentRepository;

	/** @var ComponentPositionForm @inject */
	public $componentPositionForm;

	/** @var ComponentRegister @inject */
	public $componentRegister;

	/** @var ComponentInPositionEntity */
	protected $entity;

	/** @var ComponentEntity */
	private $component;


    /** actions ***************************************************************/

	public function actionCreate()
	{
		if (!$this->user->isAllowed('admin.componentPosition', 'create')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}

		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}

		$this->component = $this->componentRepository->get(['id' => $this->id]);

		if (!$this->component) {
			$this->flashMessage(_('This component does not exist.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}

		if ($this->component->getStatus() == ComponentRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This component is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}

        // Form redirect
        $url = $this->componentRegister[$this->component->getType()]->getLinkDetail($this->component);
        $this->context->getService($this->getFormBuilderServiceAlias())->redirectTo($url, 'url');
	}


    public function actionEdit()
	{
		if (!$this->user->isAllowed('admin.componentPosition', 'edit')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}

		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}

		$this->entity = $this->repository->get(['id' => $this->id]);

		if (!$this->entity) {
			$this->flashMessage(_('This component in position does not exist.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}

		if ($this->entity->getPosition()->getStatus() == PositionRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This position is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}

		if ($this->entity->getComponent()->getStatus() == ComponentRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This component is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
	}


    public function actionDelete()
	{
		if (!$this->user->isAllowed('admin.componentPosition', 'delete')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}

		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}

		$this->entity = $this->repository->get(['id' => $this->id]);

		if (!$this->entity) {
			$this->flashMessage(_('This component in position does not exist.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}

		if ($this->entity->getPosition()->getStatus() == PositionRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This position is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}

		if ($this->entity->getComponent()->getStatus() == ComponentRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This component is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
	}


    /** handles ***************************************************************/

	public function handleDelete()
	{
		if (!$this->user->isAllowed('admin.componentPosition', 'delete')) {
			$this->flashMessage(_('For this action you do not have enough privileges.'), 'danger');
			$this->redirect('Admin:Dashboard:');
		}

		$componentInPosition = $this->repository->get(['id' => $this->id]);

		$this->repository->remove(['id' => $this->id]);

		$this->flashMessage(sprintf(_('Component %s has been successfully removed from %s position.'), $componentInPosition->getComponent()->getTitle(), $componentInPosition->getPosition()->getTitle()), 'success');

		$linkDetail = $this->componentRegister[$componentInPosition->getComponent()->getType()]->getLinkDetail($componentInPosition->getComponent());
		$this->redirectUrl($linkDetail);
	}


    /** renders ***************************************************************/

	public function renderCreate()
	{
		$this->template->siteTitle = sprintf(_("Add '%s' to position"), $this->component->getTitle());
	}


    public function renderEdit()
	{
		$this->template->siteTitle = sprintf(_("Edit '%s' in position"), $this->entity->getComponent()->getTitle());
		$this->template->subTitle = $this->entity->getPosition()->getTitle();
	}


	public function renderDelete()
	{
		$this->template->siteTitle = sprintf(_("Delete '%s' from position"), $this->entity->getComponent()->getTitle());
		$this->template->subTitle = $this->entity->getPosition()->getTitle();

		$this->template->cancelLink = $this->componentRegister->getByName($this->entity->getComponent()->getType())->getLinkDetail($this->entity->getComponent());
	}


    /** components ************************************************************/

    /**
	 * Edit component in position
	 *
	 * @return ComponentPositionForm
	 */
	protected function createComponentComponentPositionEditForm()
	{
		return $this->componentPositionForm->setId($this->id)->build();
	}


    /** abstract methods ******************************************************/

    /** {@inheritdoc} */
    protected function getFormBuilderServiceAlias()
    {
        return 'Admin.Form.ComponentPosition';
    }

}
