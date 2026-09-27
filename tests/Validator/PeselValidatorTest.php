<?php

namespace App\Tests\Validator;

use App\Validator\Pesel;
use App\Validator\PeselValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PeselValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): PeselValidator
    {
        return new PeselValidator();
    }

    /**
     * @dataProvider validPeselProvider
     */
    public function testValidPesel(string $pesel): void
    {
        $this->validator->validate($pesel, new Pesel());

        $this->assertNoViolation();
    }

    public function validPeselProvider(): array
    {
        return [
            ['44051401359'],
            ['02070803628'],
            ['00210100004'], // 2000-01-01
            ['00222900016'], // 29 lutego 2000
            ['99923100007'], // 1899-12-31
            ['00410100024'], // 2100-01-01
        ];
    }

    public function testEmptyValueIsIgnored(): void
    {
        $this->validator->validate('', new Pesel());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider invalidFormatProvider
     */
    public function testInvalidFormat(string $pesel): void
    {
        $constraint = new Pesel();
        $this->validator->validate($pesel, $constraint);

        $this->buildViolation($constraint->formatMessage)->assertRaised();
    }

    public function invalidFormatProvider(): array
    {
        return [
            ['4405140135'],
            ['440514013591'],
            ['4405140135a'],
            [' 44051401359'],
        ];
    }

    /**
     * @dataProvider invalidDateProvider
     */
    public function testInvalidDate(string $pesel): void
    {
        $constraint = new Pesel();
        $this->validator->validate($pesel, $constraint);

        $this->buildViolation($constraint->dateMessage)->assertRaised();
    }

    public function invalidDateProvider(): array
    {
        return [
            ['44130501234'], // miesiac 13
            ['44000501234'], // miesiac 00
            ['44023001234'], // 30 lutego
            ['00022900000'], // 1900 nie byl przestepny
        ];
    }

    public function testInvalidChecksum(): void
    {
        $constraint = new Pesel();
        $this->validator->validate('44051401358', $constraint);

        $this->buildViolation($constraint->checksumMessage)->assertRaised();
    }
}
