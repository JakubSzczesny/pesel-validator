<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Pesel extends Constraint
{
    public string $formatMessage = 'Numer PESEL musi składać się z 11 cyfr.';
    public string $dateMessage = 'Numer PESEL zawiera nieprawidłową datę urodzenia.';
    public string $checksumMessage = 'Nieprawidłowa cyfra kontrolna numeru PESEL.';
}
