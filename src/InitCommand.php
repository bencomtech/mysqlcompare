<?php namespace LLoadout\MysqlCompare;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends BaseCommand
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Create an empty connections file in this folder');
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
        $connectionString = '
{
  "source": {
    "dbname": "",
    "user": "",
    "password": "",
    "host": "127.0.0.1",
    "driver": "pdo_mysql",
    "ssh": ""
  },
  "target": {
    "dbname": "",
    "user": "",
    "password": "",
    "host": "127.0.0.1",
    "driver": "pdo_mysql",
    "ssh": ""
  },
  "sqlfile": "comparison.sql"
}';
        $file = fopen('connections.json', "w") or die("Unable to open file!");
        fwrite($file, $connectionString);
        fclose($file);
        return 0;
    }
}
