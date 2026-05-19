<?php

namespace App\Services\Payroll;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class FormulaEvaluatorService
{
    private $expressionLanguage;

    public function __construct()
    {
        //$this->expressionLanguage = new ExpressionLanguage();
    }

    public function evaluate(string $formula, array $variables): mixed
    {
        return $this->expressionLanguage->evaluate($formula, $variables);
    }
}
