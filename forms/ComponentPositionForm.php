<?php

namespace Wame\ComponentModule\Forms;

use Nette\Application\UI\Form;
use Kdyby\Doctrine\EntityManager;
use Wame\Utils\CacheManager;
use Wame\Core\Forms\FormFactory;
use Wame\ComponentModule\Models\ComponentManager;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;

class ComponentPositionForm extends FormFactory
{	
	/** @var EntityManager */
	private $entityManager;

	/** @var CacheManager */
	private $cacheManager;
	
	/** @var ComponentManager */
	private $componentManager;
	
	/** @var ComponentInPositionEntity */
	public $componentInPositionEntity;
	
	/** @var ComponentInPositionRepository */
	private $componentInPositionRepository;
	
	
	public function __construct(
		EntityManager $entityManager, 
		CacheManager $cacheManager,
		ComponentManager $componentManager,
		ComponentInPositionRepository $componentInPositionRepository
	) {
		parent::__construct();

		$this->entityManager = $entityManager;
		$this->cacheManager = $cacheManager;
		$this->componentManager = $componentManager;
		$this->componentInPositionRepository = $componentInPositionRepository;
	}

	
	public function build()
	{		
		$form = $this->createForm();

		$form->addSubmit('submit', _('Edit'));

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

			$this->cleanCache($componentInPositionEntity->component->cacheTag);

			$presenter->flashMessage(_('The component in position has been successfully updated.'), 'success');

			$linkDetail = $this->componentManager->components[$componentInPositionEntity->component->type]->getLinkDetail($componentInPositionEntity->component);

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
			'cache' => $values['cache'],
			'class' => $values['class'],
			'template' => $values['template']
		];
		
		return array_replace($parameters, $array);
	}
	
	
	/**
	 * Clean cache by tag
	 * 
	 * @param string $tag
	 */
	private function cleanCache($tag)
	{
		$cache = $this->cacheManager;
		$cache->setTag($tag);
		$cache->cleanByTag();
	}

}
