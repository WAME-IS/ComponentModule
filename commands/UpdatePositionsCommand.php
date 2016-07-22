<?php

namespace Wame\ComponentModule\Commands;

use Nette\Mail\SmtpException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wame\ComponentModule\Commands\Seeker\PositionsSeeker;
use Wame\ComponentModule\Entities\PositionEntity;
use Wame\ComponentModule\Entities\PositionLangEntity;
use Wame\ComponentModule\Repositories\PositionRepository;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class UpdatePositionsCommand extends Command
{

    /** @var PositionRepository */
    private $positionRepository;

    /** @var PositionsSeeker */
    private $positionsSeeker;

    public function injectServices(PositionRepository $positionRepository, PositionsSeeker $positionsSeeker)
    {
        $this->positionRepository = $positionRepository;
        $this->positionsSeeker = $positionsSeeker;
    }

    protected function configure()
    {
        $this->setName('position:update')
            ->setDescription('Updates positions tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $output->writeLn('Starting seeking of positions');

            $positions = $this->positionsSeeker->seek();

            $output->writeln('Seeking of positions ended');

            $this->savePositions($positions);

            $output->writeln('Positions updated');

            return 0; // zero return code means everything is ok
        } catch (SmtpException $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');
            return 1; // non-zero return code means error
        }
    }

    private function savePositions($positions)
    {
//        $dontRemoveId = [];
        $newPositionEntities = [];

        foreach ($positions as $position) {
            $positionEntity = $this->positionRepository->get(['name' => $position]);
            if ($positionEntity) {
//                $dontRemoveId[] = $positionEntity->id;
            } else {
                $newPositionEntity = new PositionEntity();

                $newPositionEntity->setName($position);
                $newPositionEntity->setStatus(PositionRepository::STATUS_ENABLED);
                $newPositionEntity->setCreateDate(new \DateTime());
                
                $newPositionLangEntity = new PositionLangEntity();
                $newPositionLangEntity->setPosition($newPositionEntity);
                $newPositionLangEntity->setTitle($position);
                $newPositionLangEntity->setEditDate(new \DateTime());
                $newPositionLangEntity->setLang($this->positionRepository->lang);
                $newPositionEntity->addLang($this->positionRepository->lang, $newPositionLangEntity);

                $newPositionEntities[] = $newPositionEntity;
            }
        }

//        $remove = $this->positionRepository->find(['id NIN' => $dontRemoveId]);
//        foreach ($remove as $removeEntity) {
//            $removeEntity->status = PositionRepository::STATUS_REMOVE;
//        }
        foreach ($newPositionEntities as $positionEntity) {
            $this->positionRepository->create($positionEntity);
        }
        $this->positionRepository->entityManager->flush();
    }
}
