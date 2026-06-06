<?php
namespace Training\Hello\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorld extends Command
{
    public function __construct(
    ) {
        parent::__construct();
    }
    
    protected function configure()
    {
        $this->setName('training:hello')
            ->setDescription('Hello World Command');
    }

    protected function execute
    (InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello, Magento Ninja! You\'re back in the game.');
        return Command::SUCCESS;
    }
}