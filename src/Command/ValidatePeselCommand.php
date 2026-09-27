<?php

namespace App\Command;

use App\Service\PeselDecoder;
use App\Validator\Pesel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'app:pesel:validate', description: 'Sprawdza poprawność numeru PESEL')]
class ValidatePeselCommand extends Command
{
    public function __construct(private ValidatorInterface $validator, private PeselDecoder $decoder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('pesel', InputArgument::REQUIRED, 'Numer PESEL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pesel = $input->getArgument('pesel');

        $violations = $this->validator->validate($pesel, [new NotBlank(), new Pesel()]);
        if (count($violations) > 0) {
            $io->error($violations[0]->getMessage());

            return Command::FAILURE;
        }

        $io->success('Numer PESEL jest poprawny.');
        $io->listing([
            'Data urodzenia: '.$this->decoder->getBirthDate($pesel)->format('Y-m-d'),
            'Płeć: '.$this->decoder->getGender($pesel),
        ]);

        return Command::SUCCESS;
    }
}
