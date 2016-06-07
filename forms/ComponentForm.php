<?php

namespace Wame\ComponentModule\Forms;

use Nette\Application\UI\Form;
use Nette\Security\User;
use Kdyby\Doctrine\EntityManager;
use Wame\Utils\CacheManager;
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

	/** @var CacheManager */
	private $cacheManager;

	/** @var User */
	private $user;

	/** @var UserEntity */
	private $userEntity;

	/** @var UserRepository */
	private $userRepository;
	
	/** @var ComponentEntity */
	public $componentEntity;
	
	/** @var ComponentRepository */
	public $componentRepository;
	
	/** @var string */
	public $lang;
	
	/** @var string */
	private $type;
	
	
	public function __construct(
		User $user,
		EntityManager $entityManager, 
		CacheManager $cacheManager,
		ComponentRepository $componentRepository,
		UserRepository $userRepository
	) {
		parent::__construct();

		$this->user = $user;
		$this->entityManager = $entityManager;
		$this->cacheManager = $cacheManager;
		$this->componentRepository = $componentRepository;
		$this->userRepository = $userRepository;
		
		$this->lang = $componentRepository->lang;
	}

	
	public function build()
	{		
		$form = $this->createForm();

		if ($this->id) {
			$form->addSubmit('submit', _('Save'));
		} else {
			$form->addSubmit('submit', _('Create'));
		}
		
		if ($this->id) {
			$this->componentEntity = $this->componentRepository->get(['id' => $this->id]);
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
				$componentEntity = $this->update($values);
				
				$this->componentRepository->onUpdate($form, $values, $componentEntity);

				$this->cleanCache($componentEntity->cacheTag);

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
	
	
	/**
	 * Update component
	 * 
	 * @param array $values
	 * @return ComponentEntity
	 */
	private function update($values)
	{
		$componentEntity = $this->componentRepository->get(['id' => $this->id]);
		$componentEntity->setParameters($this->getParams($values));
		
		$componentLangEntity = $componentEntity->langs[$this->lang];
		$componentLangEntity->setTitle($values['title']);
		$componentLangEntity->setDescription($values['description']);
		$componentLangEntity->setEditDate($this->formatDate('now'));
		$componentLangEntity->setEditUser($this->userEntity);
		
		return $this->componentRepository->update($componentEntity);
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