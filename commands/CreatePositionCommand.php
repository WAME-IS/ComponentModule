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


abstract class CreatePositionCommand extends Command
{
    /** @var EntityManager */
    private $entityManager;

    /** @var PositionRepository */
    private $positionRepository;

    /** @var OutputInterface */
    private $output;


    public function injectServices(
        EntityManager $entityManager,
        PositionRepository $positionRepository
    ) {
        $this->entityManager = $entityManager;
        $this->positionRepository = $positionRepository;
    }


    protected function configure()
    {
        $this->setName($this->getExecCommand())
                ->setDescription($this->getPositionDescription());
    }


    public function getExecCommand()
    {
        return 'position:' . lcfirst($this->getPositionName()) . ':create';
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->output = $output;

            $this->output->writeLn('<info>START</info> ' . $this->getPositionName());

            $positionEntity = $this->positionRepository->get(['name' => $this->getPositionName()]);

            if ($positionEntity) {
                $method = 'UPDATE';
                $this->update($positionEntity);
            } else {
                $method = 'CREATE';
                $this->create();
            }

            $this->entityManager->flush();

            $output->writeLn('<info>' . $method . '</info> ' . $this->getPositionName());

            return 0; // zero return code means everything is ok
        }
        catch (SmtpException $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');

            return 1; // non-zero return code means error
        }
    }


    /**
     * Create position
     */
    private function create()
    {
        $positionEntity = (new PositionEntity())
                            ->setName($this->getPositionName())
                            ->setParameters($this->getPositionParameters())
                            ->setInList($this->inList() === true ? 1 : 0)
                            ->setStatus(PositionRepository::STATUS_ENABLED)
                            ->setCreateDate(new \DateTime());

        $positionLangEntity = (new PositionLangEntity())
                                ->setPosition($positionEntity)
                                ->setTitle($this->getPositionTitle())
                                ->setDescription($this->getPositionDescription())
                                ->setEditDate(new \DateTime())
                                ->setLang($this->positionRepository->lang);

        $positionEntity->addLang($this->positionRepository->lang, $positionLangEntity);

        $this->positionRepository->create($positionEntity);

        $this->output->writeLn(sprintf('CREATE <info>%s</info> position', $this->getPositionName()));
    }


    /**
     * Update position
     *
     * @param PositionEntity $positionEntity
     */
    private function update($positionEntity)
    {
        $positionEntity->setParameters($this->getPositionParameters());
        $positionEntity->setInList($this->inList() === true ? 1 : 0);

        foreach ($positionEntity->getLangs() as $positionLangEntity) {
            $positionLangEntity->setTitle($this->getPositionTitle());
            $positionLangEntity->setDescription($this->getPositionDescription());
        }
    }


    /** abstract methods ******************************************************/

    /**
     * Get unique position name
     *
     * @return string camelCase
     */
    abstract protected function getPositionName();


    /**
     * Get translated position title
     *
     * @return string _('translation')
     */
    abstract protected function getPositionTitle();


    /**
     * Get translated position description
     *
     * @return string _('translation')
     */
    abstract protected function getPositionDescription();


    /**
     * Get position parameters
     *
     * @return array container|template...
     */
    abstract protected function getPositionParameters();


    /**
     * Get position in list
     * show in grid
     *
     * @return boolean
     */
    abstract protected function inList();

}
