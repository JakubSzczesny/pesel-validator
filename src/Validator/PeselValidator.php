<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PeselValidator extends ConstraintValidator
{
    private const WEIGHTS = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Pesel) {
            throw new UnexpectedTypeException($constraint, Pesel::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!preg_match('/^\d{11}$/', $value)) {
            $this->context->buildViolation($constraint->formatMessage)->addViolation();

            return;
        }

        if (!$this->hasValidDate($value)) {
            $this->context->buildViolation($constraint->dateMessage)->addViolation();

            return;
        }

        if (!$this->hasValidChecksum($value)) {
            $this->context->buildViolation($constraint->checksumMessage)->addViolation();
        }
    }

    private function hasValidDate(string $pesel): bool
    {
        $year = (int) substr($pesel, 0, 2);
        $month = (int) substr($pesel, 2, 2);
        $day = (int) substr($pesel, 4, 2);

        // do miesiaca dodawane jest 20 za kazde stulecie (1800-1899 to +80)
        $centuries = [1900, 2000, 2100, 2200, 1800];
        $index = intdiv($month, 20);

        return checkdate($month - $index * 20, $day, $centuries[$index] + $year);
    }


    private function hasValidChecksum(string $pesel): bool
    {
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += self::WEIGHTS[$i] * (int) $pesel[$i];
        }

        return (10 - $sum % 10) % 10 === (int) $pesel[10];
    }
}
