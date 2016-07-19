<?php

namespace Wame\ComponentModule\Forms;

use Nette\Utils\Html;
use Nette\Application\UI\Form;
use Kdyby\Doctrine\EntityManager;
use Wame\Utils\CacheManager;
use Wame\Core\Forms\FormFactory;
use Wame\ComponentModule\Registers\ComponentRegister;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Repositories\PositionRepository;

class ComponentAddToPositionForm extends FormFactory
{	
	/** @var string */
	private $lang;
	
	/** @var EntityManager */
	private $entityManager;
	
	/** @var ComponentRegister */
	private $componentRegister;
	
	/** @var ComponentEntity */
	private $componentEntity;

	/** @var ComponentRepository */
	private $componentRepository;
	
	/** @var ComponentInPositionEntity */
	public $componentInPositionEntity;
	
	/** @var ComponentInPositionRepository */
	private $componentInPositionRepository;
	
	/** @var PositionRepository */
	private $positionRepository;
	
	
	public function __construct(
		EntityManager $entityManager,
		ComponentRegister $componentRegister,
		ComponentRepository $componentRepository,
		ComponentInPositionRepository $componentInPositionRepository,
		PositionRepository $positionRepository
	) {
		parent::__construct();

		$this->entityManager = $entityManager;
		$this->componentRegister = $componentRegister;
		$this->componentRepository = $componentRepository;
		$this->componentInPositionRepository = $componentInPositionRepository;
		$this->positionRepository = $positionRepository;
		
		$this->lang = $positionRepository->lang;
	}

	
	public function build()
	{		
		$form = $this->createForm();

		$form->addSelect('position', _('Position'), $this->getPositionList())
				->setPrompt('- ' . _('Select position') . ' -');
		
		$form->addSubmit('submit', _('Add to position'));

		$form->onSuccess[] = [$this, 'formSucceeded'];

		return $form;
	}
	
	public function formSucceeded(Form $form, $values)
	{
		$presenter = $form->getPresenter();

		try {
			$this->componentEntity = $this->componentRepository->get(['id' => $presenter->getParameter('id')]);
			
			$componentInPositionEntity = $this->add($values);

			$this->componentInPositionRepository->onCreate($form, $values, $componentInPositionEntity);

			$presenter->flashMessage(_('The component was successfully added to the position.'), 'success');
			$linkDetail = $this->componentRegister[$componentInPositionEntity->component->type]->getLinkDetail($componentInPositionEntity->component);
			$presenter->redirectUrl($linkDetail);
		} catch (\Exception $e) {
			if ($e instanceof \Nette\Application\AbortException) {
				throw $e;
			}
			
			$form->addError($e->getMessage());
			$this->entityManager->clear();
		}
	}

	
	/**
	 * Add component to position
	 * 
	 * @param array $values
	 * @return ComponentInPositionEntity
	 */
	private function add($values)
	{
		$position = $this->positionRepository->get(['id' => $values['position']]);
		
		$componentInPositionEntity = new ComponentInPositionEntity();
		$componentInPositionEntity->component = $this->componentEntity;
		$componentInPositionEntity->position = $position;
		$componentInPositionEntity->setSort(0);
		$componentInPositionEntity->setParameters(null);
		
		return $this->componentInPositionRepository->create($componentInPositionEntity);
	}
	
	
	/**
	 * Return positions
	 * disabled if it is used position
	 * 
	 * @return array
	 */
	private function getPositionList()
	{
		$return = [];
		
		$component = $this->componentRepository->get(['id' => $this->id]);
		$componentInPosition = $this->componentInPositionRepository->getPositions($component);

		$positions = $this->positionRepository->find(['status !=' => PositionRepository::STATUS_REMOVE]);
		
		foreach ($positions as $position) {
			$title = $position->langs[$this->lang]->title;
			
			if ($position->status == PositionRepository::STATUS_DISABLED) {
				$title .= ' [' . _('Disabled') . ']';
			}
			
			$option = Html::el('option')->value($position->id)->setText($title);
			
			if (isset($componentInPosition[$position->id])) {
				$option->disabled(true);
			}
			
			$return[$position->id] = $option;
		}
		
		return $return;
	}

}
