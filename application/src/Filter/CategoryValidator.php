<?php

namespace App\Filter\Interfaces;

use App\Filter\AbstractValidator;

class CategoryValidator extends AbstractValidator
{
    public function __construct()
    {
        parent::__construct('categories');
    }
}
