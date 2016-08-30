<?php

namespace Wame\ComponentModule\Macros;

use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Utils\Strings;

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
        return $writer->write('$_positionName = %node.word;'
                . 'if(\Wame\ComponentModule\Components\PositionControlLoader::checkStatic($_control, $_positionName)) {'
                    . '$_position = $_control->getComponent("position".Nette\Utils\Strings::firstUpper($_positionName));'
                    . 'if ($_position instanceof \Wame\Core\Components\BaseControl) {'
                        . ($node->modifiers === '' ? "\$_position->willRender(\"render\");" : $writer->write("ob_start(function () {}); \$_position->willRender(\"render\"); echo %modify(ob_get_clean())"))
                    . '} else {'
                        . ($node->modifiers === '' ? "\$_position->render();" : $writer->write("ob_start(function () {}); \$_position->render(); echo %modify(ob_get_clean())"))
                    . '}'
                . '} else {'
                    . 'echo "<script type=\"text/javascript\">location.reload();</script>";'
                . '}'
                . '$_position=null;$_positionName=null;');
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
        $paramList = $param;
        if (!Strings::contains($node->args, '=>')) {
            $paramList = substr($paramList, $paramList[0] === '[' ? 1 : 6, -1); // removes array() or []
        }
        return ($name[0] === '$' ? "if (is_object($name)) \$_l->tmp = $name; else " : '')
            . '$_l->tmp = $_control->getComponent(' . $name . '); '
            . 'if ($_l->tmp instanceof \Nette\Application\UI\IRenderable) $_l->tmp->redrawControl(NULL, FALSE); '
            . 'if ($_l->tmp instanceof \Wame\Core\Components\BaseControl) {'
            . ($node->modifiers === '' ? "\$_l->tmp->willRender(\"$method\", $param);" : $writer->write("ob_start(function () {}); \$_l->tmp->willRender(\"$method\", $param); echo %modify(ob_get_clean())"))
            . '} else {'
            . ($node->modifiers === '' ? "\$_l->tmp->$method($paramList);" : $writer->write("ob_start(function () {}); \$_l->tmp->$method($paramList); echo %modify(ob_get_clean())"))
            . '}';
    }
}
