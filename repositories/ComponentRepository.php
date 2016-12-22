<?php

namespace Wame\ComponentModule\Repositories;

use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Entities\ComponentLangEntity;
use Wame\Core\Exception\RepositoryException;
use Wame\LanguageModule\Repositories\TranslatableRepository;

class ComponentRepository extends TranslatableRepository
{
    const STATUS_REMOVE = 0;
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    const HIDE_IN_LIST = 0;
    const SHOW_IN_LIST = 1;


    public function __construct()
    {
        parent::__construct(ComponentEntity::class, ComponentLangEntity::class);
    }

    /**
     * Return component status list
     *
     * @return array
     */
    public function getStatusList()
    {
        return [
            self::STATUS_REMOVE => _('Remove'),
            self::STATUS_ENABLED => _('Enabled'),
            self::STATUS_DISABLED => _('Disabled')
        ];
    }

    /**
     * Return component status
     *
     * @param int $status
     * @return string
     */
    public function getStatus($status)
    {
        return $this->getStatusList()[$status];
    }

    /**
     * Create component
     *
     * @param ComponentEntity $componentEntity
     * @return ComponentEntity
     * @throws RepositoryException
     */
    public function create($componentEntity)
    {
        $this->componentExists($componentEntity);

        $this->entityManager->persist($componentEntity);

        $this->entityManager->persist($componentEntity->langs);

        return $componentEntity;
    }

    /**
     * Update component
     *
     * @param ComponentEntity $componentEntity
     * @return ComponentEntity
     */
    public function update($componentEntity)
    {
        $this->componentExists($componentEntity, $componentEntity->id);

        return $componentEntity;
    }

    /**
     * Delete component by criteria
     *
     * @param array $criteria
     * @param int $status
     */
    public function delete($criteria = [], $status = self::STATUS_REMOVE)
    {
        $entity = $this->get($criteria);
        $entity->setStatus($status);
    }

    /**
     * Check component exists
     *
     * @param ComponentEntity $componentEntity
     * @param mixed $without - without component ids
     * @return mixed
     * @throws RepositoryException
     */
    public function componentExists($componentEntity, $without = null)
    {
        $criteria = ['name' => $componentEntity->name, 'status !=' => self::STATUS_REMOVE];

        if ($without) {
            if (!is_array($without)) {
                $without = [$without];
            }

            $criteria['id NOT IN'] = $without;
        }

        $component = $this->get($criteria);

        if ($component) {
            throw new RepositoryException(_('Component with this name already exists.'));
        } else {
            return null;
        }
    }
}
