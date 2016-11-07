<?php

namespace App\AdminModule\Presenters;

use Wame\DynamicObject\Vendor\Wame\AdminModule\Presenters\AdminFormPresenter;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\ComponentModule\Registers\ComponentRegister;
use Wame\ComponentModule\Components\PositionListControl;
use Wame\ComponentModule\Components\IPositionListControlFactory;
use Wame\ComponentModule\Components\ComponentPositionListControl;
use Wame\ComponentModule\Components\IComponentPositionListControlFactory;
use Wame\ComponentModule\Vendor\Wame\MenuModule\Components\ComponentMenu\ItemTemplate;
use Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\ComponentGrid;
use Wame\ComponentModule\Vendor\Wame\AdminModule\Grids\CreateComponentGrid;
use Wame\MenuModule\Components\MenuControl;
use Wame\ComponentModule\Doctrine\Filters\ComponentStatusFilter;
use Wame\ComponentModule\Components\IAddComponentControlFactory;


class ComponentPresenter extends AdminFormPresenter
{
	/** @var array */
	public $components = [];

	/** @var ComponentEntity */
	public $entity;

	/** @var ComponentRepository @inject */
	public $repository;

	/** @var ComponentRegister @inject */
	public $componentRegister;

	/** @var IPositionListControlFactory @inject */
	public $IPositionListControlFactory;

	/** @var IComponentPositionListControlFactory @inject */
	public $IComponentPositionListControlFactory;

	/** @var IAddComponentControlFactory @inject */
	public $IAddComponentControlFactory;

	/** @var ItemTemplate @inject */
	public $componentItemTemplate;

    /** @var ComponentGrid @inject */
	public $componentGrid;

    /** @var CreateComponentGrid @inject */
	public $createComponentGrid;

    /** @var ComponentStatusFilter @inject */
	public $componentStatusFilter;


    /** actions ***************************************************************/

	public function actionDefault()
	{
		if (!$this->user->isAllowed('component', 'default')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');
		}

        $this->componentStatusFilter->setEnabled(false);

        $qb = $this->repository->createQueryBuilder('a');
        $qb->andWhere($qb->expr()->eq('a.inList', ComponentRepository::SHOW_IN_LIST));

        $this->components = $qb;
    }


	public function actionDelete()
	{
		if (!$this->user->isAllowed('component', 'delete')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');
		}

        if (!$this->id) {
            $this->flashMessage(_('Missing identifier.'));
            $this->redirect(':Admin:Component:', ['id' => null]);
        }

        $this->componentStatusFilter->setEnabled(false);

        $this->entity = $this->repository->get(['id' => $this->id]);

        if (!$this->entity) {
            $this->flashMessage(_('Component does not exists.'));
            $this->redirect(':Admin:Component:', ['id' => null]);
        }

        if ($this->entity->getStatus() == ComponentRepository::STATUS_DELETED) {
            $this->flashMessage(_('Component is already deleted.'));
            $this->redirect(':Admin:Component:', ['id' => null]);
        }
    }


	/** handles ***************************************************************/

	public function handleDelete()
	{
		if (!$this->user->isAllowed('component', 'delete')) {
			$this->flashMessage(_('For this action you do not have enough privileges.'), 'danger');
			$this->redirect('Admin:Dashboard:');
		}

		$this->repository->delete(['id' => $this->id]);

		$this->flashMessage(_('Component has been successfully deleted.'), 'success');
		$this->redirect(':Admin:Component:', ['id' => null]);
	}


    /** renders ***************************************************************/

	public function renderDefault()
	{
		$this->template->siteTitle = _('Components');
		$this->template->count = count($this->components);
	}


	public function renderCreate()
	{
		$this->template->siteTitle = _('Select a type component');
	}


	public function renderDelete()
	{
		$this->template->siteTitle = _('Delete component');
		$this->template->subTitle = $this->entity->getTitle();
	}


    /** components ************************************************************/

    /**
	 * Add component component
	 *
	 * @return MenuControl
	 */
	protected function createComponentAddComponent()
	{
        $control = $this->IAddComponentControlFactory->create();

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
	 * Component position list component
	 *
	 * @return ComponentPositionListControl
	 */
	protected function createComponentComponentPositionList()
	{
        $control = $this->IComponentPositionListControlFactory->create();

		return $control;
	}


    /**
     * Component grid component
     *
     * @param type $name
     * @return type
     */
    protected function createComponentComponentGrid()
	{
		$this->componentGrid->setDataSource($this->components);

		return $this->componentGrid;
	}


    /**
     * Component component grid
     *
     * @param type $name
     * @return type
     */
    protected function createComponentCreateComponentGrid()
	{
//        $qb = $this->repository->createQueryBuilder('a');
		$this->createComponentGrid->setDataSource($this->getComponentsArray());

		return $this->createComponentGrid;
	}


    /**
     * Get component array
     *
     * @return ComponentEntity[]    components
     */
    private function getComponentsArray()
    {
        $components = [];

        foreach($this->componentRegister->getAll() as $i => $component) {
            /* @var $component \Wame\ComponentModule\Registers\IComponent */

            $components[] = (object) [
                'id' => $i,
                'name' => $component->getName(),
                'description' => $component->getDescription(),
                'createAction' => $component->getLinkCreate()
            ];
        }

        return $components;
    }


    /** abstract methods ******************************************************/

    /** {@inheritdoc} */
    protected function getFormBuilderServiceAlias() { }

}
