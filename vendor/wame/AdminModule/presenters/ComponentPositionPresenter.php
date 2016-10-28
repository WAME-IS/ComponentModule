<?php

namespace App\AdminModule\Presenters;

use Wame\DynamicObject\Vendor\Wame\AdminModule\Presenters\AdminFormPresenter;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;


class ComponentPositionPresenter extends AdminFormPresenter
{
	/** @var ComponentInPositionRepository @inject */
	public $repository;

	/** @var ComponentInPositionEntity */
	protected $entity;


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
		$this->template->siteTitle = _('Add component to position');
	}


	public function renderDelete()
	{
		$this->template->siteTitle = _('Delete component from position');
		$this->template->subTitle = $this->entity->getPosition()->getTitle();

		$this->template->cancelLink = $this->componentRegister->getByName($this->entity->getComponent()->getType())->getLinkDetail($this->entity->getComponent());
	}


    /** abstract methods ******************************************************/

    /** {@inheritdoc} */
    protected function getFormBuilderServiceAlias()
    {
        return 'Admin.Form.ComponentPosition';
    }

}
