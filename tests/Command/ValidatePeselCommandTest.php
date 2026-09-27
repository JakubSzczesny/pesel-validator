<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ValidatePeselCommandTest extends KernelTestCase
{
    public function testValidPesel(): void
    {
        $tester = $this->createTester();
        $tester->execute(['pesel' => '44051401359']);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertStringContainsString('1944-05-14', $tester->getDisplay());
    }

    public function testInvalidPesel(): void
    {
        $tester = $this->createTester();
        $tester->execute(['pesel' => '44051401358']);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
    }

    private function createTester(): CommandTester
    {
        $application = new Application(self::bootKernel());

        return new CommandTester($application->find('app:pesel:validate'));
    }
}
