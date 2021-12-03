<?php namespace LLoadout\MysqlCompare;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Compare extends BaseCommand
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('compare')
            ->setDescription('Compare databases');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $databaseManager = new DatabaseManager();
        $comparison      = $databaseManager->compare()->saveToFile();
        return 0;
    }
}