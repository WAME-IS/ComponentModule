<?php

namespace Wame\ComponentModule\Paremeters\Readers;

class ParameterReaders
{
    public static $HTML;
}

ParameterReaders::$HTML = [
    'container' => [
        'class' => new ConcatParameterReader(" "),
        'style' => new ConcatParameterReader(";\n")
    ]
];
