<?php

namespace Wame\ComponentModule\Forms;

use Nette\Application\UI\Form;
use Nette\Security\User;
use Wame\Utils\Strings;
use Kdyby\Doctrine\EntityManager;
use Wame\Core\Forms\FormFactory;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Entities\ComponentLangEntity;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\UserModule\Entities\UserEntity;
use Wame\UserModule\Repositories\UserRepository;

class ComponentForm extends FormFactory
{	
	/** @var EntityManager */
	private $entityManager;

	/** @var User */
	private $user;

	/** @var UserEntity */
	private $userEntity;

	/** @var UserRepository */
	private $userRepository;
	
	/** @var ComponentRepository */
	private $componentRepository;
	
	/** @var string */
	private $lang;
	
	/** @var string */
	private $type;
	
	
	public function __construct(
		User $user,
		EntityManager $entityManager, 
		ComponentRepository $componentRepository,
		UserRepository $userRepository
	) {
		parent::__construct();

		$this->user = $user;
		$this->entityManager = $entityManager;
		$this->componentRepository = $componentRepository;
		$this->userRepository = $userRepository;
		
		$this->lang = $componentRepository->lang;
	}

	
	public function build()
	{		
		$form = $this->createForm();

		if ($this->id) {
			$form->addSubmit('submit', _('Update'));
		} else {
			$form->addSubmit('submit', _('Create'));
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
				$componentEntity = $this->update($values);
				
				$this->componentRepository->onUpdate($form, $values, $componentEntity);

				$presenter->flashMessage(_('The component has been successfully updated.'), 'success');
			} else {
				$componentEntity = $this->create($values);
				
				$this->componentRepository->onCreate($form, $values, $componentEntity);

				$presenter->flashMessage(_('The component was successfully created.'), 'success');
			}

			$presenter->redirect(':Admin:Component:', ['id' => null]);
		} catch (\Exception $e) {
			if ($e instanceof \Nette\Application\AbortException) {
				throw $e;
			}
			
			$form->addError($e->getMessage());
			$this->entityManager->clear();
		}
	}
	
	
	/**
	 * Create component
	 * 
	 * @param array $values
	 * @return ComponentEntity
	 */
	private function create($values)
	{
		$componentEntity = new ComponentEntity();
		$componentEntity->setType($this->getType());
		$componentEntity->setName($this->getComponentName($values));
		$componentEntity->setParameters($this->getParams($values));
		$componentEntity->setCreateDate($this->formatDate('now'));
		$componentEntity->setCreateUser($this->userEntity);
		$componentEntity->setStatus(ComponentRepository::STATUS_ENABLED);
		
		$componentLangEntity = new ComponentLangEntity();
		$componentLangEntity->component = $componentEntity;
		$componentLangEntity->setLang($this->lang);
		$componentLangEntity->setTitle($values['title']);
		$componentLangEntity->setDescription($values['description']);
		$componentLangEntity->setEditDate($this->formatDate('now'));
		$componentLangEntity->setEditUser($this->userEntity);
		
		$componentEntity->addLang($this->lang, $componentLangEntity);
		
		return $this->componentRepository->create($componentEntity);
	}
	
	
	private function update($values)
	{
		
	}
	
	
	/**
	 * Set component type
	 * 
	 * @param string $type
	 * @return \Wame\ComponentModule\Forms\ComponentForm
	 */
	public function setType($type)
	{
		$this->type = $type;
		
		return $this;
	}
	
	
	/**
	 * Get component type
	 * 
	 * @return string
	 * @throws \Exception
	 */
	public function getType()
	{
		if ($this->type) {
			return $this->type;
		} else {
			throw new \Exception(_('Type is not defined. You must use the setType() method to create a form.'));
		}
	}

	
	/**
	 * Get component name
	 * 
	 * @param array $values
	 * @return string
	 */
	private function getComponentName($values)
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
			'cache' => $values['cache'],
			'class' => $values['class'],
			'template' => $values['template']
		];
		
		return array_replace($parameters, $array);
	}

}
