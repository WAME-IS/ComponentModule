<?php

namespace Wame\ComponentModule\Forms;

use Nette\Application\UI\Form;
use Kdyby\Doctrine\EntityManager;
use Wame\Core\Forms\FormFactory;
use Wame\ComponentModule\Registers\ComponentRegister;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Paremeters\ContainerAttributes;


class ComponentPositionForm extends FormFactory
{	
	/** @var EntityManager */
	private $entityManager;
	
	/** @var ComponentRegister */
	private $componentRegister;
	
	/** @var ComponentEntity */
	public $componentEntity;
	
	/** @var ComponentInPositionEntity */
	public $componentInPositionEntity;
	
	/** @var ComponentInPositionRepository */
	private $componentInPositionRepository;
	
	
	public function __construct(
		EntityManager $entityManager,
		ComponentRegister $componentRegister,
		ComponentInPositionRepository $componentInPositionRepository
	) {
		parent::__construct();

		$this->entityManager = $entityManager;
		$this->componentRegister = $componentRegister;
		$this->componentInPositionRepository = $componentInPositionRepository;
	}

	
	public function build()
	{		
		$form = $this->createForm();

		$this->componentInPositionEntity = $this->componentInPositionRepository->get(['id' => $this->id]);
		$this->setDefaultValues();
		
		$form->onSuccess[] = [$this, 'formSucceeded'];

		return $form;
	}
	
	public function formSucceeded(Form $form, $values)
	{
		$presenter = $form->getPresenter();

		try {
			$componentInPositionEntity = $this->update($values);

			$this->componentInPositionRepository->onUpdate($form, $values, $componentInPositionEntity);

			$presenter->flashMessage(_('The component in position has been successfully updated.'), 'success');

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
	 * Update position
	 * 
	 * @param array $values
	 * @return ComponentInPositionEntity
	 */
	private function update($values)
	{
		$this->componentInPositionEntity->setParameters($this->getParams($values));
		
		return $this->componentInPositionRepository->update($this->componentInPositionEntity);
	}
	
	
	/**
	 * Get parameters
	 * 
	 * @param array $values
	 * @param array $parameters
	 * @return array
	 */
	private function getParams($values, $parameters = [])
	{
		$array = [
			'container' => ContainerAttributes::toDatabase($values['container']),
			'template' => $values['template']
		];
		
		return array_replace($parameters, $array);
	}

}
