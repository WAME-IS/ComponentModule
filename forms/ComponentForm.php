<?php

namespace Wame\ComponentModule\Forms;

use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Utils\Strings;
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

	/** @var UserEntity */
	private $userEntity;
	
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

		$this->entityManager = $entityManager;
		$this->componentRepository = $componentRepository;
		
		$this->userEntity = $userRepository->get(['id' => $user->id]);
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
		$componentEntity->type = $this->getType();
		$componentEntity->name = $this->getComponentName($values);
		$componentEntity->cache = $values['cache'];
		$componentEntity->template = $values['template'];
		$componentEntity->createDate = $this->formatDate('now');
		$componentEntity->createUser = $this->userEntity;
		$componentEntity->status = ComponentRepository::STATUS_ENABLED;
		
		$componentLangEntity = new ComponentLangEntity();
		$componentLangEntity->component = $componentEntity;
		$componentLangEntity->lang = $this->lang;
		$componentLangEntity->title = $values['title'];
		$componentLangEntity->description = $values['description'];
		$componentLangEntity->editDate = $this->formatDate('now');
		$componentLangEntity->editUser = $this->userEntity;
		
		return $this->componentRepository->create($componentLangEntity);
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
			throw new \Exception(_('Type is not defined. You must use the settType() method to create a form.'));
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
		
		return Strings::webalize($name);
	} 

}
