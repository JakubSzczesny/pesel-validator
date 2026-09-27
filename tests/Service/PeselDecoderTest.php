<?php

namespace App\Tests\Service;

use App\Service\PeselDecoder;
use PHPUnit\Framework\TestCase;

class PeselDecoderTest extends TestCase
{
    /**
     * @dataProvider birthDateProvider
     */
    public function testGetBirthDate(string $pesel, string $expected): void
    {
        $decoder = new PeselDecoder();

        $this->assertSame($expected, $decoder->getBirthDate($pesel)->format('Y-m-d'));
    }

    public function birthDateProvider(): array
    {
        return [
            ['44051401359', '1944-05-14'],
            ['00210100004', '2000-01-01'],
            ['99923100007', '1899-12-31'],
            ['00410100024', '2100-01-01'],
            ['00610100037', '2200-01-01'],
        ];
    }

    public function testInvalidDateReturnsNull(): void
    {
        $decoder = new PeselDecoder();

        $this->assertNull($decoder->getBirthDate('44023001234'));
    }

    public function testGetGender(): void
    {
        $decoder = new PeselDecoder();

        $this->assertSame('mężczyzna', $decoder->getGender('44051401359'));
        $this->assertSame('kobieta', $decoder->getGender('02070803628'));
    }
}
