<?php

namespace App\AdminModule\Presenters;

use Wame\DynamicObject\Vendor\Wame\AdminModule\Presenters\AdminFormPresenter;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Components\PositionListControl;
use Wame\ComponentModule\Components\IPositionListControlFactory;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\ComponentInPositionGrid;
use Wame\ComponentModule\Forms\PositionForm;
use Wame\ComponentModule\Doctrine\Filters\ComponentStatusFilter;
use Wame\ComponentModule\Doctrine\Filters\PositionStatusFilter;


class PositionPresenter extends AdminFormPresenter
{
	/** @var PositionRepository @inject */
	public $repository;

	/** @var IPositionListControlFactory @inject */
	public $IPositionListControlFactory;

    /** @var ComponentInPositionRepository @inject */
    public $componentInPositionRepository;

    /** @var ComponentInPositionGrid @inject */
	public $componentInPositionGrid;

    /** @var PositionForm @inject */
	public $positionForm;

    /** @var ComponentStatusFilter @inject */
	public $componentStatusFilter;

    /** @var PositionStatusFilter @inject */
	public $positionStatusFilter;

	/** @var PositionEntity */
	protected $entity;

	/** @var ComponentEntity[] */
	private $components;


    /** actions ***************************************************************/

    public function actionDefault()
    {
		if (!$this->user->isAllowed('position', 'default')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}

        $this->positionStatusFilter->setEnabled(false);

        parent::actionDefault();
    }


	public function actionShow()
	{
		if (!$this->user->isAllowed('position', 'show')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}

		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}

        $this->positionStatusFilter->setEnabled(false);

		$this->entity = $this->repository->get(['id' => $this->id]);

		if (!$this->entity) {
			$this->flashMessage(_('This position does not exist.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}

		if ($this->entity->getStatus() == PositionRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This position is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}

        $this->componentStatusFilter->setEnabled(false);

        $qb = $this->componentInPositionRepository->createQueryBuilder('a');
        $qb->join(\Wame\ComponentModule\Entities\ComponentEntity::class, 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 'a.component = c.id');
        $qb->andWhere($qb->expr()->eq('a.position', $this->id));
        $qb->andWhere($qb->expr()->neq('c.status', 0));

        $this->components = $qb;
	}


	public function actionCreate()
	{
		if (!$this->user->isAllowed('position', 'create')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}
	}


	public function actionEdit()
	{
		if (!$this->user->isAllowed('position', 'edit')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}

		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}

		$this->entity = $this->repository->get(['id' => $this->id]);

		if (!$this->entity) {
			$this->flashMessage(_('This position does not exist.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}

		if ($this->entity->getStatus() == PositionRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This position is removed.'), 'danger');
			$this->redirect(':Admin:Component:', ['id' => null]);
		}
	}


	public function actionDelete()
	{
		if (!$this->user->isAllowed('position', 'delete')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:', ['id' => null]);
		}

		if (!$this->id) {
			$this->flashMessage(_('Missing identifier.'), 'danger');
			$this->redirect(':Admin:Component:');
		}

		$this->entity = $this->repository->get(['id' => $this->id]);

		if (!$this->entity) {
			$this->flashMessage(_('This position does not exist.'), 'danger');
			$this->redirect(':Admin:Position:', ['id' => null]);
		}

		if ($this->entity->getStatus() == PositionRepository::STATUS_REMOVE) {
			$this->flashMessage(_('This position is removed.'), 'danger');
			$this->redirect(':Admin:Position:', ['id' => null]);
		}
	}


    /** handles ***************************************************************/

	public function handleDelete()
	{
		if (!$this->user->isAllowed('position', 'delete')) {
			$this->flashMessage(_('For this action you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');
		}

		$this->repository->delete(['id' => $this->id]);

		$this->flashMessage(_('Position has been successfully deleted.'), 'success');
		$this->redirect(':Admin:Position:', ['id' => null]);
	}


	/**
	 * Change position status
	 * enabled/disabled
	 */
	public function handleChangeStatus()
	{
		if (!$this->user->isAllowed('position', 'changeStatus')) {
			$this->flashMessage(_('For this action you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');
		}

		$this->repository->changeStatus(['id' => $this->id]);

        if ($this->isAjax()) {
            $this->redrawControl('positionStatus');
        } else {
            $this->redirect('this');
        }
	}


    /**
     * Sort position
     *
     * @param int $item_id
     * @param int $prev_id
     * @param int $next_id
     */
    public function handleSort($item_id, $prev_id, $next_id)
    {
        $this->componentInPositionRepository->move($item_id, $prev_id, $next_id);

        $this->flashMessage(
            "Id: $item_id, Previous id: $prev_id, Next id: $next_id",
            'success'
        );

        if ($this->isAjax()) {
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }


    /** renders ***************************************************************/

    public function renderDefault()
    {
        $this->template->siteTitle = _('Positions');
        $this->template->count = $this->count;
    }

	public function renderShow()
	{
		$this->template->siteTitle = _('Position');
		$this->template->subTitle = $this->entity->getTitle();
		$this->template->position = $this->entity;
		$this->template->components = $this->components;
		$this->template->positionStatusList = $this->repository->getStatusList();
	}


	public function renderCreate()
	{
		$this->template->siteTitle = _('Create position');
	}


	public function renderUpdate()
	{
		$this->template->siteTitle = _('Update position');
		$this->template->subTitle = $this->entity->getTitle();
	}


	public function renderDelete()
	{
		$this->template->siteTitle = _('Delete position');
		$this->template->subTitle = $this->entity->getTitle();
	}


    /** components ************************************************************/

    /**
	 * Position form component
	 *
	 * @return PositionForm
	 */
	protected function createComponentPositionForm()
	{
		$control = $this->positionForm
							->setId($this->id)
							->build();

		return $control;
	}


	/**
	 * Position list component
	 *
	 * @return PositionListControl
	 */
	protected function createComponentPositionList()
	{
        $control = $this->IPositionListControlFactory->create();

		return $control;
	}


    /**
     * ComponentInPosition grid component
     *
     * @return ComponentInPositionGrid
     */
    protected function createComponentComponentInPositionGrid()
	{
		$this->componentInPositionGrid->setDataSource($this->components);
		$this->componentInPositionGrid->setSortable();
        $this->componentInPositionGrid->setDefaultSort(['sort' => 'ASC']);

		return $this->componentInPositionGrid;
	}


    /** abstract methods ******************************************************/

    /** {@inheritdoc} */
    protected function getFormBuilderServiceAlias()
    {
        return 'Admin.Form.Position';
    }


    /** {@inheritdoc} */
    protected function getGridServiceAlias()
    {
        return 'Admin.Grid.Position';
    }

}
