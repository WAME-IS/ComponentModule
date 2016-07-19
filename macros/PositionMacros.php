<?php

namespace Wame\ComponentModule\Macros;

use Latte\Macros\MacroSet;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\PhpWriter;

class PositionMacros extends MacroSet
{
    public static function install(Compiler $compiler)
    {
        $set = new static($compiler);
		
        $set->addMacro('position', [$set, 'macroPosition']);
    }


    public function macroPosition(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write('$position = $_control->getComponent("position".Nette\Utils\Strings::firstUpper(%node.word));'.
            '$position->render();'.
            '$position=null;');
    }

}