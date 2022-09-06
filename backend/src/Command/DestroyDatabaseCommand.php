<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DestroyDatabaseCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'destroy:database';

    protected function configure(): void
    {
        $this
            ->addArgument('env', InputArgument::OPTIONAL, 'Which environment you want to destroy?')
            ->addArgument('quiet', null, InputOption::VALUE_NONE);

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try{

            $env = $input->getArgument('env');
            switch($env){
                case 'dev':
                    break;
                case 'test':
                    break;
                default:
                    $env = 'dev';
                    $output->writeln('No env specified, will use dev environment!');
                    $output->writeln('Next try use php bin/console destroy:database [<env>]');

            }
            if($input->getArgument('quiet')){
                $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
            }

            // outputs a message followed by a "\n"
            $output->writeln('Whoa!');
            $output->writeln('You are about to DESTROY THE DATABAAAAAAASE');
            
            $command = $this->getApplication()->find('doctrine:database:drop');
            $arguments = new ArrayInput([
                '--force' => true,
                '--no-interaction' => true,
                '--env' => $env,
            ]);
            
            $arguments->setInteractive(false);
            $returnCode = $command->run($arguments, $output);
    
            $arguments = new ArrayInput([
                '--no-interaction' => true,
                '--env' => $env,
            ]);
            
            $arguments->setInteractive(false);
            $command = $this->getApplication()->find('doctrine:database:create');
            $returnCode = $command->run($arguments, $output);

            $command = $this->getApplication()->find('doctrine:schema:create');
            $returnCode = $command->run($arguments, $output);

            $command = $this->getApplication()->find('doctrine:fixtures:load');
            $returnCode = $command->run($arguments, $output);

            $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
            $output->writeln('Database successfully recreated');

            return Command::SUCCESS;

        } 
        catch (Exception $e){
            return Command::FAILURE;
        }
    }
}