<?php

namespace Wame\ComponentModule\Paremeters\Readers;

class ParameterReaders
{

    public static $HTML;

}

ParameterReaders::$HTML = [
    'class' => new ConcatParameterReader(" "),
    'style' => new ConcatParameterReader(";\n")
];
