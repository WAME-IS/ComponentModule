<?php

namespace Wame\ComponentModule\Commands;

use Nette\Mail\SmtpException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kdyby\Doctrine\EntityManager;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Entities\ComponentLangEntity;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Entities\PositionLangEntity;
use Wame\ComponentModule\Repositories\ComponentRepository;
use Wame\ComponentModule\Repositories\ComponentInPositionRepository;
use Wame\ComponentModule\Repositories\PositionRepository;


abstract class CreateComponentCommand extends Command
{
    /** @var EntityManager */
    private $entityManager;

    /** @var ComponentRepository */
    private $componentRepository;

    /** @var ComponentInPositionRepository */
    private $componentInPositionRepository;

    /** @var PositionRepository */
    private $positionRepository;

    /** @var OutputInterface */
    private $output;


    public function injectServices(
        EntityManager $entityManager,
        ComponentRepository $componentRepository,
        ComponentInPositionRepository $componentInPositionRepository,
        PositionRepository $positionRepository
    ) {
        $this->entityManager = $entityManager;
        $this->componentRepository = $componentRepository;
        $this->componentInPositionRepository = $componentInPositionRepository;
        $this->positionRepository = $positionRepository;
    }


    protected function configure()
    {
        $this->setName('component:'.$this->getComponentName().':create')
                ->setDescription($this->getComponentDescription());
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->output = $output;
            $positionList = $this->getPositions();

            $this->output->writeLn('<info>START</info> ' . $this->getComponentType());

        // Component

            $this->output->writeLn('FIND component');
            $componentEntity = $this->getComponent();

        // Position
            $this->output->writeLn('FIND positions');
            $positions = $this->findPositions($positionList);
            $countPositions = count($positions);
            $countPositionList = count($positionList);

            if ($countPositions < $countPositionList) {
                $this->output->writeLn(sprintf('Find %s of %s', $countPositions, $countPositionList));
            } else {
                $this->output->writeLn('All positions are created');
            }

            $positionEntities = $this->createPositionsIfNotExist($positionList, $positions);

        // Component in position
            $this->output->writeLn('FIND component in positions');
            $componentInPositions = $this->findComponentInPositions($componentEntity);
            $countComponentInPositions = count($componentInPositions);

            if ($countComponentInPositions < $countPositionList) {
                $this->output->writeLn(sprintf('Find %s of %s', $countComponentInPositions, $countPositionList));
                $this->addComponentToPositions($componentEntity, $positionList, $positionEntities, $componentInPositions);
            } else {
                $this->output->writeLn('Component is added to all positions');
            }

            $this->entityManager->flush();

            $output->writeLn('<info>END</info> ' . $this->getComponentType());

            return 0; // zero return code means everything is ok
        }
        catch (SmtpException $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');

            return 1; // non-zero return code means error
        }
    }


    /**
     * Get component
     * if not exist create component
     *
     * @return ComponentEntity
     */
    private function getComponent()
    {
        $componentEntity = $this->componentRepository->get(['name' => $this->getComponentName(), 'type' => $this->getComponentType()]);

        if (!$componentEntity) {
            $this->output->writeLn('Component does not exist');

            $componentEntity = $this->createComponent();
        } else {
            $this->output->writeLn('Component is exist');
        }

        return $componentEntity;
    }


    /**
     * Create component
     *
     * @return ComponentEntity
     */
    private function createComponent()
    {
        $componentEntity = (new ComponentEntity())
                            ->setName($this->getComponentName())
                            ->setType($this->getComponentType())
                            ->setParameters($this->getComponentParameters())
                            ->setStatus(ComponentRepository::STATUS_ENABLED)
                            ->setInList(ComponentRepository::SHOW_IN_LIST);

        $lang = $this->componentRepository->lang;

        $componentLangEntity = (new ComponentLangEntity())
                                ->setComponent($componentEntity)
                                ->setLang($lang)
                                ->setTitle($this->getComponentTitle())
                                ->setDescription($this->getComponentDescription());

        $componentEntity->addLang($lang, $componentLangEntity);

        $this->output->writeLn('CREATE component');

        return $componentEntity;
    }


    /**
     * Find positions
     *
     * @param array $positionList
     * @return PositionEntity[];
     */
    private function findPositions($positionList)
    {
        return $this->positionRepository->findAssoc(['name IN' => array_keys($positionList)], 'name');
    }


    /**
     * Create positions
     *
     * @param array $positionList
     * @param PositionEntity[] $positions
     * @return PositionEntity[]
     */
    private function createPositionsIfNotExist($positionList, $positions)
    {
        $return = [];

        foreach ($positionList as $name => $parameters) {
            if (isset($positions[$name])) {
                $return[$name] = $positions[$name];
            } else {
                $positionEntity = (new PositionEntity())
                                    ->setName($name)
                                    ->setStatus(PositionRepository::STATUS_ENABLED)
                                    ->setCreateDate(new \DateTime());

                $positionLangEntity = (new PositionLangEntity())
                                        ->setPosition($positionEntity)
                                        ->setTitle($name)
                                        ->setEditDate(new \DateTime())
                                        ->setLang($this->positionRepository->lang);

                $positionEntity->addLang($this->positionRepository->lang, $positionLangEntity);

                $return[$name] = $this->positionRepository->create($positionEntity);

                $this->output->writeLn(sprintf('CREATE <info>%s</info> position', $name));
            }
        }

        return $return;
    }


    /**
     * Find component in positions
     *
     * @param CompoenntEntity $componentEntity
     * @return array;
     */
    private function findComponentInPositions($componentEntity)
    {
        $return = [];

        $componentInPositions = $this->componentInPositionRepository->find(['component' => $componentEntity]);

        foreach ($componentInPositions as $componentInPosition) {
            $positionName = $componentInPosition->getPosition()->getName();

            $return[$positionName] = $positionName;
        }

        return $return;
    }


    /**
     * Add component to positions
     *
     * @param CompoenntEntity $componentEntity
     * @param array $positionList
     * @param PositionEntity[] $positionEntities
     * @param array $componentInPositions
     */
    private function addComponentToPositions($componentEntity, $positionList, $positionEntities, $componentInPositions)
    {
        foreach ($positionList as $name => $parameters) {
            if (!isset($componentInPositions[$name])) {
                $positionEntity = $positionEntities[$name];
                $sort = $this->componentInPositionRepository->getNextSort(['position' => $positionEntity, 'component' => $componentEntity]);

                $componentInPositionEntity = (new ComponentInPositionEntity())
                                                ->setPosition($positionEntity)
                                                ->setComponent($componentEntity)
                                                ->setParameters($parameters)
                                                ->setSort($sort);

                $this->componentInPositionRepository->create($componentInPositionEntity);

                $this->output->writeLn(sprintf('ADD component to <info>%s</info> position', $name));
            }
        }
    }


    /** abstract methods ******************************************************/

    /**
     * Get unique component name
     *
     * @return string camelCase
     */
    abstract protected function getComponentName();


    /**
     * Get component type
     *
     * @return string CamelCase
     */
    abstract protected function getComponentType();


    /**
     * Get translated component title
     *
     * @return string _('translation')
     */
    abstract protected function getComponentTitle();


    /**
     * Get translated component description
     *
     * @return string _('translation')
     */
    abstract protected function getComponentDescription();


    /**
     * Get component parameters
     *
     * @return array container|template...
     */
    abstract protected function getComponentParameters();


    /**
     * Get component in position
     *
     * @return array
     */
    abstract protected function getPositions();

}
