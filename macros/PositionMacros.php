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
        $set->addMacro('control', [$set, 'macroControl']);
    }

    public function macroPosition(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write('$_positionName = %node.word;' .
                '\Wame\ComponentModule\Components\PositionControlLoader::checkStatic($_control, $_positionName);' .
                '$_position = $_control->getComponent("position".Nette\Utils\Strings::firstUpper($_positionName), false);' .
                '$_position->render();' .
                '$_position=null;$_positionName=null;');
    }
    
    /**
	 * {control name[:method] [params]}
	 */
	public function macroControl(MacroNode $node, PhpWriter $writer)
	{
		$words = $node->tokenizer->fetchWords();
		if (!$words) {
			throw new CompileException('Missing control name in {control}');
		}
		$name = $writer->formatWord($words[0]);
		$method = isset($words[1]) ? ucfirst($words[1]) : '';
		$method = Strings::match($method, '#^\w*\z#') ? "render$method" : "{\"render$method\"}";
		$param = $writer->formatArray();
		if (!Strings::contains($node->args, '=>')) {
			$param = substr($param, $param[0] === '[' ? 1 : 6, -1); // removes array() or []
		}
		return ($name[0] === '$' ? "if (is_object($name)) \$_l->tmp = $name; else " : '')
			. '$_l->tmp = $_control->getComponent(' . $name . '); '
			. 'if ($_l->tmp instanceof Nette\Application\UI\IRenderable) $_l->tmp->redrawControl(NULL, FALSE); '
			. ($node->modifiers === '' ? "\$_l->tmp->$method($param)" : $writer->write("ob_start(function () {}); \$_l->tmp->willRender($method, $param); echo %modify(ob_get_clean())"));
	}
}
