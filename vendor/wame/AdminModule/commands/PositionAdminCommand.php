<?php

namespace Wame\ComponentModule\Vendor\Wame\AdminModule\Commands;

use Nette\Mail\SmtpException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Entities\PositionLangEntity;
use Wame\ComponentModule\Repositories\PositionRepository;


class PositionAdminCommand extends Command
{
    /** @var PositionRepository */
    private $positionRepository;


    public function injectServices(PositionRepository $positionRepository)
    {
        $this->positionRepository = $positionRepository;
    }


    protected function configure()
    {
        $this->setName('position:admin:create')
                ->setDescription('Create admin positions if not exists');
    }


    private function getPositionList()
    {
        return [
            'adminBeforeContent',
            'adminAfterContent',
            'adminHeaderLeft',
            'adminHeaderRight'
        ];
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $positionList = $this->getPositionList();

            $output->writeLn('<info>START</info> Find admin positions in database');

            $positions = $this->findPositions($positionList);

            $countPositions = count($positions);
            $countPositionList = count($positionList);

            if ($countPositions < $countPositionList) {
                $output->writeLn(sprintf('Find %s of %s', $countPositions, $countPositionList));

                $this->createPositions($output, $positionList, $positions);
            } else {
                $output->writeLn('All admin positions are created, not added any new');
            }

            $output->writeLn('<info>END</info> create admin positions');

            return 0; // zero return code means everything is ok
        }
        catch (SmtpException $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');

            return 1; // non-zero return code means error
        }
    }


    private function findPositions($positionList)
    {
        return $this->positionRepository->findPairs(['name IN' => $positionList], 'name', 'name');
    }


    private function createPositions($output, $positionList, $positions)
    {
        foreach ($positionList as $position) {
            if (!isset($positions[$position])) {
                $positionEntity = (new PositionEntity())
                                    ->setName($position)
                                    ->setStatus(PositionRepository::STATUS_ENABLED)
                                    ->setCreateDate(new \DateTime());

                $positionLangEntity = (new PositionLangEntity())
                                        ->setPosition($positionEntity)
                                        ->setTitle($position)
                                        ->setEditDate(new \DateTime())
                                        ->setLang($this->positionRepository->lang);

                $positionEntity->addLang($this->positionRepository->lang, $positionLangEntity);

                $this->positionRepository->create($positionEntity);

                $output->writeLn(sprintf('CREATE <info>%s</info> position', $position));
            }
        }

        $this->positionRepository->entityManager->flush();

        return $output;
    }

}
