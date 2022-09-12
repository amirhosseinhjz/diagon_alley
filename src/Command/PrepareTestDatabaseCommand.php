<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'test:prepare-test-database',
    description: 'Add a short description for your command',
)]
class PrepareTestDatabaseCommand extends Command
{
    protected function configure(): void{}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        shell_exec('sh /var/www/html/shellScript/testDatabase/regenerate.sh');
        return Command::SUCCESS;
    }
}
