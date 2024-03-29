<?php

namespace App\Validator;

use App\Services\Constants\RussianNouns;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckRusNounValidator extends ConstraintValidator
{
    private RussianNouns $russianNouns;

    public function __construct(RussianNouns $russianNouns)
    {

        $this->russianNouns = $russianNouns;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\CheckRusNoun $constraint */

        if (!$value || in_array(mb_strtolower($value), $this->russianNouns->getNouns())) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
