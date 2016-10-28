<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Commands;

use Nette\Mail\SmtpException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kdyby\Doctrine\EntityManager;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Entities\PositionLangEntity;
use Wame\ComponentModule\Repositories\PositionRepository;


class PositionAdminCommand extends Command
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
        $this->setName('position:admin:update')
                ->setDescription('Update adminModule @layout positions');
    }


    private function getPositionList()
    {
        return [
            'adminBeforeContent' => [],
            'adminAfterContent' => [],
            'adminHeaderLeft' => [
                'container' => [
                    'tag' => 'ul',
                    'class' => 'left hide-on-med-and-down'
                ]
            ],
            'adminHeaderRight' => [
                'container' => [
                    'tag' => 'ul',
                    'class' => 'right hide-on-med-and-down'
                ]
            ]
        ];
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->output = $output;
            $positionList = $this->getPositionList();

            $this->output->writeLn('<info>START</info> Find admin positions in database');

            $positions = $this->findPositions($positionList);

            $countPositions = count($positions);
            $countPositionList = count($positionList);

            if ($countPositions < $countPositionList) {
                $this->output->writeLn(sprintf('Find %s of %s', $countPositions, $countPositionList));
            } else {
                $this->output->writeLn('All admin positions are created');
            }

            $this->createOrUpdatePositions($positionList, $positions);

            $this->output->writeLn('Update admin position parameters');

            $this->output->writeLn('<info>END</info> create admin positions');

            return 0; // zero return code means everything is ok
        }
        catch (SmtpException $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');

            return 1; // non-zero return code means error
        }
    }


    private function findPositions($positionList)
    {
        return $this->positionRepository->findAssoc(['name IN' => array_keys($positionList)], 'name');
    }


    private function createOrUpdatePositions($positionList, $positions)
    {
        foreach ($positionList as $name => $parameters) {
            if (!isset($positions[$name])) {
                $positionEntity = (new PositionEntity())
                                    ->setName($name)
                                    ->setParameters($parameters)
                                    ->setStatus(PositionRepository::STATUS_ENABLED);

                $lang = $this->positionRepository->lang;

                $positionLangEntity = (new PositionLangEntity())
                                        ->setPosition($positionEntity)
                                        ->setTitle($name)
                                        ->setLang($lang);

                $positionEntity->addLang($lang, $positionLangEntity);

                $this->positionRepository->create($positionEntity);

                $this->output->writeLn(sprintf('CREATE <info>%s</info> position', $name));
            } else {
                $positionEntity = $positions[$name];
                $positionEntity->setParameters($parameters);

                $this->positionRepository->update($positionEntity);
            }
        }

        $this->entityManager->flush();
    }

}
