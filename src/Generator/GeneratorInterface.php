<?php

namespace Shahrestani\Avatar\Generator;

interface GeneratorInterface
{
    public function make($name, $length, $uppercase, $ascii, $rtl);
}
