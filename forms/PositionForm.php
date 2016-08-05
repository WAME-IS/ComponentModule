<?php

namespace Wame\ComponentModule\Forms;

use Nette\Application\UI\Form;
use Nette\Security\User;
use Kdyby\Doctrine\EntityManager;
use Wame\Core\Forms\FormFactory;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Entities\PositionLangEntity;
use Wame\ComponentModule\Repositories\PositionRepository;
use Wame\UserModule\Repositories\UserRepository;
use Wame\UserModule\Entities\UserEntity;
use Wame\ComponentModule\Paremeters\ContainerAttributes;


class PositionForm extends FormFactory
{	
	/** @var EntityManager */
	private $entityManager;
	
	/** @var User */
	private $user;
	
	/** @var UserEntity */
	private $userEntity;
	
	/** @var UserRepository */
	private $userRepository;
	
	/** @var PositionRepository */
	private $positionRepository;
	
	/** @var PositionEntity */
	public $positionEntity;
	
	/** @var string */
	public $lang;
	
	
	public function __construct(
		User $user,
		EntityManager $entityManager, 
		PositionRepository $positionRepository,
		UserRepository $userRepository
	) {
		parent::__construct();

		$this->user = $user;
		$this->entityManager = $entityManager;
		$this->positionRepository = $positionRepository;
		$this->userRepository = $userRepository;
		
		$this->lang = $positionRepository->lang;
	}

	
	public function build()
	{		
		$form = $this->createForm();
		
		if ($this->id) {
			$this->positionEntity = $this->positionRepository->get(['id' => $this->id]);
			$this->setDefaultValues();
		}
		
		$form->onSuccess[] = [$this, 'formSucceeded'];

		return $form;
	}
	
	public function formSucceeded(Form $form, $values)
	{
		$presenter = $form->getPresenter();

		$this->userEntity = $this->userRepository->get(['id' => $this->user->id]);

		try {
			if ($this->id) {
				$positionEntity = $this->update($values);
				
				$this->positionRepository->onUpdate($positionEntity, $form, $values);

				$presenter->flashMessage(_('The position has been successfully updated.'), 'success');
				$presenter->redirect(':Admin:Position:show', ['id' => $this->id]);
			} else {
				$positionEntity = $this->create($values);
				
				$this->positionRepository->onCreate($positionEntity, $form, $values);

				$presenter->flashMessage(_('The position was successfully created.'), 'success');
				$presenter->redirect(':Admin:Component:', ['id' => null]);
			}

		} catch (\Exception $e) {
			if ($e instanceof \Nette\Application\AbortException) {
				throw $e;
			}
			
			$form->addError($e->getMessage());
			$this->entityManager->clear();
		}
	}
	
	
	/**
	 * Create position
	 * 
	 * @param array $values
	 * @return PositionEntity
	 */
	private function create($values)
	{
		$positionEntity = new PositionEntity();
		$positionEntity->setName($this->getPositionName($values));
		$positionEntity->setParameters($this->getParams($values));
		$positionEntity->setCreateDate(\Wame\Utils\Date::toDateTime('now'));
		$positionEntity->setCreateUser($this->userEntity);
		$positionEntity->setStatus(PositionRepository::STATUS_ENABLED);

		$positionLangEntity = new PositionLangEntity();
		$positionLangEntity->position = $positionEntity;
		$positionLangEntity->setTitle($values['title']);
		$positionLangEntity->setLang($this->lang);
		$positionLangEntity->setDescription($values['description']);
		$positionLangEntity->setEditDate(\Wame\Utils\Date::toDateTime('now'));
		$positionLangEntity->setEditUser($this->userEntity);

		$positionEntity->addLang($this->lang, $positionLangEntity);
		
		return $this->positionRepository->create($positionEntity);
	}
	
	
	/**
	 * Update position
	 * 
	 * @param array $values
	 * @return PositionEntity
	 */
	private function update($values)
	{
		$this->positionEntity->setName($values['name']);
		$this->positionEntity->setParameters($this->getParams($values, $this->positionEntity->parameters));

		$positionLangEntity = $this->positionEntity->langs[$this->lang];
		$positionLangEntity->setTitle($values['title']);
		$positionLangEntity->setDescription($values['description']);
		$positionLangEntity->setEditDate(\Wame\Utils\Date::toDateTime('now'));
		$positionLangEntity->setEditUser($this->userEntity);
		
		return $this->positionRepository->update($this->positionEntity);
	}

	
	/**
	 * Get position name
	 * 
	 * @param array $values
	 * @return string
	 */
	private function getPositionName($values)
	{
		if ($values['name']) {
			$name = $values['name'];
		} else {
			$name = $values['title'];
		}
		
		return $name;
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
			'container' => ContainerAttributes::toDatabase($values['container'])
		];
		
		return array_replace($parameters, $array);
	}

}
