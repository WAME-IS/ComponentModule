<?php

namespace Wame\ComponentModule\Commands\Seeker;

use Nette\InvalidStateException;
use Nette\Object;
use Nette\Utils\Finder;
use Nette\Utils\Strings;

class PositionsSeeker extends Object
{

    const POSITION_PATTERN = '-\{position (.*?)\}-';
    //const POSITION_PATTERN = '-\{position (.*?)\}|\{control position(.*?)\}-';

    /** @var string[] */
    private $directories;

    public function __construct($directories = null)
    {
        $this->directories = $directories;
        
        $this->addDirectory(VENDOR_PATH.'/'.PACKAGIST_NAME);
        $this->addDirectory(TEMPLATES_PATH);
        $this->addDirectory(APP_PATH);
    }

    public function addDirectory($directory)
    {
        $this->directories[] = $directory;
    }

    public function seek()
    {
        $positions = [];

        if (!$this->directories) {
            throw new InvalidStateException("You have to add at least one directory to PositionSeeker.");
        }

        foreach ($this->directories as $directory) {
            foreach (Finder::find("*.latte")->from($directory) as $file) {
                $latte = file_get_contents($file);
                $match = Strings::match($latte, self::POSITION_PATTERN);
                
                if($match && !in_array($match[1], $positions)) {
                    $positions[] = $match[1];
                }
            }
        }

        return $positions;
    }
}
