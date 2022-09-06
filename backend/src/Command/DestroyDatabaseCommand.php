<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DestroyDatabaseCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'destroy:database:test';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        // outputs a message followed by a "\n"
        $output->writeln('Whoa!');
        $output->writeln('You are about to DESTROY THE DATABAAAAAAASE');
        

        $command = $this->getApplication()->find('doctrine:database:drop');

        $arguments = [
            '--force' => true,
            '--env' => 'test'
        ];

        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);



        $command = $this->getApplication()->find('doctrine:database:create');
        $arguments = [
            '--env' => 'test'
        ];
        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);



        $command = $this->getApplication()->find('doctrine:schema:create');
        $arguments = [
            '--no-interaction' => true,
            '--env' => 'test'
        ];
        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);


        $arguments = new ArrayInput([
            '--env' => 'test'
        ]);
        $arguments->setInteractive(false);
        
        $command = $this->getApplication()->find('doctrine:fixtures:load');
        $returnCode = $command->run($arguments, $output);

        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}