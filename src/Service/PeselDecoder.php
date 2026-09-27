<?php

namespace App\Service;

class PeselDecoder
{
    public function getBirthDate(string $pesel): ?\DateTimeImmutable
    {
        $year = (int) substr($pesel, 0, 2);
        $month = (int) substr($pesel, 2, 2);
        $day = (int) substr($pesel, 4, 2);

        // do miesiaca dodawane jest 20 za kazde stulecie (1800-1899 to +80)
        $centuries = [1900, 2000, 2100, 2200, 1800];
        $index = intdiv($month, 20);
        $year += $centuries[$index];
        $month -= $index * 20;

        if (!checkdate($month, $day, $year)) {
            return null;
        }

        return new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $day));
    }

    public function getGender(string $pesel): string
    {
        return (int) $pesel[9] % 2 === 0 ? 'kobieta' : 'mężczyzna';
    }
}
