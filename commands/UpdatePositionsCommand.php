<?php

namespace Wame\ComponentModule\Commands;

use Nette\Mail\SmtpException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wame\ComponentModule\Repositories\PositionRepository;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class UpdatePositionsCommand extends Command {

	/** @var PositionRepository */
	private $positionRepository;

    /** @var PositionsSeeker */
    private $positionsSeeker;
    
	public function injectServices(PositionRepository $positionRepository, PositionsSeeker $positionsSeeker) {
		$this->positionRepository = $positionRepository;
	
        $this->positionsSeeker = $positionsSeeker;
    }

	protected function configure() {
		$this->setName('position:update')
				->setDescription('Updates positions tables');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		
		try {
			
			$output->writeLn('Starting seeking of positions');
            
            $positions = $this->positionsSeeker->seek();
            
            $output->writeln('Seeking of positions ended');
            
//            $positions
            
            $output->writeln('Positions updated');
			
            return 0; // zero return code means everything is ok
		} catch (SmtpException $e) {
			$output->writeLn('<error>' . $e->getMessage() . '</error>');
			return 1; // non-zero return code means error
		}
	}

}
