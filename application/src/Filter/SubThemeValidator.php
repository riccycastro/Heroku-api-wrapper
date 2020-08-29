<?php

namespace App\Filter\Interfaces;

use App\Filter\AbstractValidator;

class SubThemeValidator extends AbstractValidator
{
    public function __construct()
    {
        parent::__construct('sub_themes');
    }
}
