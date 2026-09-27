<?php

namespace App\Validator;

use App\Service\PeselDecoder;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PeselValidator extends ConstraintValidator
{
    private const WEIGHTS = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];

    public function __construct(private PeselDecoder $decoder)
    {
    }

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

        if (null === $this->decoder->getBirthDate($value)) {
            $this->context->buildViolation($constraint->dateMessage)->addViolation();

            return;
        }

        if (!$this->hasValidChecksum($value)) {
            $this->context->buildViolation($constraint->checksumMessage)->addViolation();
        }
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
